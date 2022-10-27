<?php

declare(strict_types=1);

/*
 * This file is part of the isun/api
 *
 * @see https://gitea.huayaygf.io/isun/api
 */

namespace support\bootstrap;

use Monolog\Logger;
use Webman\Bootstrap;

/**
 * Class Redis.
 *
 * @method static void log($level, $message, array $context = [])
 * @method static void debug($message, array $context = [])
 * @method static void info($message, array $context = [])
 * @method static void notice($message, array $context = [])
 * @method static void warning($message, array $context = [])
 * @method static void error($message, array $context = [])
 * @method static void critical($message, array $context = [])
 * @method static void alert($message, array $context = [])
 * @method static void emergency($message, array $context = [])
 */
class Log implements Bootstrap
{
    /**
     * @var array
     */
    protected static $_instance = [];

    /**
     * @param \Workerman\Worker $worker
     */
    public static function start($worker): void
    {
        $configs = config('log', []);
        foreach ($configs as $channel => $config) {
            $logger = static::$_instance[$channel] = new Logger($channel);
            foreach ($config['handlers'] as $handler_config) {
                $handler = new $handler_config['class'](...array_values($handler_config['constructor']));
                if (isset($handler_config['formatter'])) {
                    $formatter = new $handler_config['formatter']['class'](...array_values($handler_config['formatter']['constructor']));
                    $handler->setFormatter($formatter);
                }
                $logger->pushHandler($handler);
            }
        }
    }

    /**
     * @param string $name
     *
     * @return Logger;
     */
    public static function channel($name = 'default')
    {
        return static::$_instance[$name] ?? null;
    }

    public static function telegram(): ?Logger
    {
        return static::$_instance['telegram'] ?? null;
    }

    /**
     * @param $name
     * @param $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        return static::channel('default')->{$name}(...$arguments);
    }
}
