<?php

declare(strict_types=1);

use Webman\Http\Response;

use support\Container;
use support\Request;
use support\Translation;
use Workerman\Worker;
use Webman\App;
use Webman\Config;
use Webman\Route;

// Webman version
define('WEBMAN_VERSION', '1.4');

function base_path(): string
{
    return BASE_PATH;
}

function app_path(): string
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'app';
}

function public_path(): string
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'public';
}

function config_path(): string
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'config';
}

function runtime_path(): string
{
    return BASE_PATH . DIRECTORY_SEPARATOR . 'runtime';
}

/**
 * @param array<string, string> $headers
 */
function response(string $body, int $status = 200, array $headers = []): Response
{
    return new Response($status, $headers, $body);
}

/**
 * @param array<string, mixed> $data
 */
function json(array $data, int $options = JSON_UNESCAPED_UNICODE): support\Response
{
    // @phpstan-ignore-next-line
    return new \support\Response(200, ['Content-Type' => 'application/json'], json_encode($data, $options));
}

function xml(string|SimpleXMLElement $xml): Response
{
    if ($xml instanceof SimpleXMLElement) {
        $xml = $xml->asXML();
    }

    // @phpstan-ignore-next-line
    return new Response(200, ['Content-Type' => 'text/xml'], $xml);
}

function jsonp(mixed $data, string $callback_name = 'callback'): Response
{
    if (!is_scalar($data) && null !== $data) {
        $data = json_encode($data);
    }

    return new Response(200, [], "{$callback_name}({$data})");
}

/**
 * @param array<string, string> $headers
 */
function redirect(string $location, int $status = 302, array $headers = []): Response
{
    $response = new Response($status, ['Location' => $location]);
    if (!empty($headers)) {
        $response->withHeaders($headers);
    }

    return $response;
}

/**
 * @param array<string, mixed> $vars
 */
function view(string $template, array $vars = [], string $app = null): Response
{
    static $handler;
    if (null === $handler) {
        $handler = config('view.handler');
    }

    return new Response(200, [], $handler::render($template, $vars, $app));
}

function request(): Request|Webman\Http\Request
{
    return App::request();
}

function config(string $key, mixed $default = null): mixed
{
    return Config::get($key, $default);
}

/**
 * @param array<string, string> $parameters
 */
function route(string $name, array $parameters = []): string
{
    $route = Route::getByName($name);
    if (!$route) {
        return '';
    }

    return $route->url($parameters);
}

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = getenv($key);

        if (false === $value) {
            return $default;
        }

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return null;
        }

        if (($valueLength = strlen($value)) > 1 && '"' === $value[0] && '"' === $value[$valueLength - 1]) {
            return substr($value, 1, -1);
        }

        return $value;
    }
}
/**
 * @param array<string, string> $parameters
 */
function trans(string $id, array $parameters = [], string $domain = null, string $locale = null): string
{
    $res = Translation::trans($id, $parameters, $domain, $locale);

    return '' === $res ? $id : $res;
}

function locale(string $locale = null): string
{
    if (!$locale) {
        return Translation::getLocale();
    }

    Translation::setLocale($locale);

    return $locale;
}

/**
 * @phpstan-ignore-next-line
 *
 * @param $worker
 * @param $class
 */
function worker_bind($worker, $class): void
{
    $callback_map = [
        'onConnect',
        'onMessage',
        'onClose',
        'onError',
        'onBufferFull',
        'onBufferDrain',
        'onWorkerStop',
        'onWebSocketConnect',
    ];

    foreach ($callback_map as $name) {
        if (method_exists($class, $name)) {
            $worker->{$name} = [$class, $name];
        }
    }

    if (method_exists($class, 'onWorkerStart')) {
        // @phpstan-ignore-next-line
        $class->onWorkerStart($worker);
    }
}

function cpu_count(): int
{
    if ('darwin' === strtolower(PHP_OS)) {
        $count = shell_exec('sysctl -n machdep.cpu.core_count');
    } else {
        $count = shell_exec('nproc');
    }

    return (int)$count > 0 ? (int)$count : 4;
}

// Project base path
define('BASE_PATH', dirname(__DIR__));

/**
 * Generate paths based on given information.
 *
 * @return string
 */
function path_combine(string $front, string $back)
{
    return $front . ($back ? (DIRECTORY_SEPARATOR . ltrim($back, DIRECTORY_SEPARATOR)) : $back);
}

function run_path(string $path = ''): string
{
    static $run_path = '';
    if (!$run_path) {
        $run_path = is_phar() ? dirname(\Phar::running(false)) : BASE_PATH;
    }

    return path_combine($run_path, $path);
}

/** @param array<mixed, mixed> $config */
function worker_start(string $process_name, array $config): void
{
    /** @phpstan-ignore-next-line  */
    $worker = new Worker($config['listen'] ?? null, $config['context'] ?? []);
    $property_map = [
        'count',
        'user',
        'group',
        'reloadable',
        'reusePort',
        'transport',
        'protocol',
    ];
    $worker->name = $process_name;
    foreach ($property_map as $property) {
        if (isset($config[$property])) {
            /* @phpstan-ignore-next-line */
            $worker->{$property} = $config[$property];
        }
    }

    $worker->onWorkerStart = function ($worker) use ($config): void {
        require_once base_path() . '/support/bootstrap.php';

        foreach (toArr($config['services'] ?? []) as $server) {
            /* @phpstan-ignore-next-line */
            if (!class_exists($server['handler'])) {
                /* @phpstan-ignore-next-line */
                echo "process error: class {$server['handler']} not exists\r\n";
                continue;
            }
            /** @phpstan-ignore-next-line  */
            $listen = new Worker($server['listen'] ?? null, $server['context'] ?? []);
            /* @phpstan-ignore-next-line */
            if (isset($server['listen'])) {
                echo "listen: {$server['listen']}\n";
            }
            /** @phpstan-ignore-next-line  */
            $instance = Container::make($server['handler'], $server['constructor'] ?? []);
            worker_bind($listen, $instance);
            $listen->listen();
        }

        if (isset($config['handler'])) {
            /* @phpstan-ignore-next-line */
            if (!class_exists($config['handler'])) {
                /* @phpstan-ignore-next-line */
                echo "process error: class {$config['handler']} not exists\r\n";

                return;
            }
            /** @phpstan-ignore-next-line  */
            $instance = Container::make($config['handler'], $config['constructor'] ?? []);
            worker_bind($worker, $instance);
        }
    };
}

/**
 * Phar support.
 * Compatible with the 'realpath' function in the phar file.
 */
function get_realpath(string $file_path): string
{
    if (str_starts_with($file_path, 'phar://')) {
        return $file_path;
    }

    return toStr(realpath($file_path));
}

function is_phar(): bool
{
    return class_exists(\Phar::class, false) && Phar::running();
}
