<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use support\bootstrap\Log;
use support\Container;
use support\marshal\InvalidConstructor;
use support\marshal\ParameterNotFound;
use support\Response;

/** @return array<string> */
function toArr(mixed $data): array
{
    if (is_string($data)) {
        return explode(',', $data);
    }

    if (is_object($data) && method_exists($data, 'toArray')) {
        return $data->toArray();
    }

    return is_array($data) ? $data : [];
}

function toStr(mixed $data, string $default = ''): string
{
    if (is_string($data)) {
        return $data;
    }
    if (is_numeric($data) || $data instanceof Stringable) {
        return (string)$data;
    }

    return $default;
}

function toInt(mixed $data, int $default = 0): int
{
    return is_numeric($data) || is_bool($data) ? (int)$data : $default;
}

function toFloat(mixed $data, float $default = 0.00): float
{
    return is_numeric($data) ? (float)$data : $default;
}

/**
 * @param array<mixed> $data
 */
function arrToStr(array $data): string
{
    return 0 === count($data) ? '' : '["' . implode('","', $data) . '"]';
}

function base64Safe(string $data): string
{
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function dateWeek(?int $time = null): string
{
    $time ??= time();
    $w = (int)('0' === date('w', $time) ? 7 : date('w', $time));

    return date('YW', (date('W', $time) >= 52) ? ($time - ($w - 1) * 24 * 3600) : ($time + (7 - $w) * 24 * 3600));
}

/**
 * @phpstan-template T of object
 *
 * @phpstan-param  class-string<T> $class
 * @phpstan-param  array<mixed, mixed> $data
 * @phpstan-return  T
 */
function unmarshal(string $class, array $data): mixed
{
    $reflection = new \ReflectionClass($class);

    if (!$reflection->getConstructor()) {
        throw InvalidConstructor::notFound($class);
    }

    if (!$reflection->getConstructor()->isPublic()) {
        throw InvalidConstructor::notPublic($class);
    }

    $properties = [];
    foreach ($reflection->getConstructor()->getParameters() as $parameter) {
        if (isset($data[$parameter->getName()])) {
            $value = $data[$parameter->getName()];
            /** @phpstan-ignore-next-line */
            $className = $parameter->getType()?->getName();
            if ($parameter->isVariadic() && is_array($value)) {
                foreach ($value as $v) {
                    /* @phpstan-ignore-next-line */
                    $properties[] = unmarshal($className, is_array($v) ? $v : ['value' => $v]);
                }
                continue;
            }
            if ((is_int($value) || is_string($value)) && is_a($className, BackedEnum::class, true)) {
                $properties[] = $className::from($value);
                continue;
            }
            if ($className && class_exists($className)) {
                $properties[] = unmarshal($className, is_array($value) ? $value : ['value' => $value]);
                continue;
            }
            $properties[] = $value;
            continue;
        }
        if ($parameter->isDefaultValueAvailable()) {
            $properties[] = $parameter->getDefaultValue();
            continue;
        }
        throw new ParameterNotFound($class, $parameter->getName());
    }

    return new $class(...$properties);
}

/**
 * @phpstan-template T of object
 *
 * @phpstan-param  class-string<T> $class
 * @phpstan-param  array<array<string, mixed>> $arr
 * @phpstan-return  array<T>
 */
function unmarshalArr(string $class, array $arr): mixed
{
    $result = [];
    foreach ($arr as $key => $item) {
        $result[$key] = unmarshal($class, $item);
    }

    return $result;
}

/**
 * @param array<object>|object $object
 *
 * @return array<string, mixed>
 */
function marshal(array|object $object, bool $skipNull = true): array
{
    if (is_array($object)) {
        return array_map(fn ($o) => marshal($o), $object);
    }

    $reflection = new \ReflectionClass($object);

    if (!$reflection->getConstructor()) {
        throw new \RuntimeException('Constructor must be specified to marshal ' . $object::class);
    }

    $data = [];
    foreach ($reflection->getConstructor()->getParameters() as $parameter) {
        if (!$parameter->isPromoted() && !$parameter->isVariadic()) {
            continue;
        }

        $propValue = $reflection->getProperty($parameter->getName())->getValue($object);
        $value = match (true) {
            $propValue instanceof BackedEnum => $propValue->value,
            is_object($propValue) && isValueObject($propValue) => (new ReflectionClass($propValue))->getProperty('value')->getValue($propValue),
            is_object($propValue),
                $parameter->isVariadic() && is_array($propValue) => marshal($propValue),
            default => $propValue,
        };
        if ($skipNull && null === $value) {
            continue;
        }
        $data[$parameter->getName()] = $value;
    }

    return $data;
}

/** @param object|class-string $object */
function isValueObject(object|string $object): bool
{
    $reflection = new \ReflectionClass($object);
    if (!$reflection->getConstructor()) {
        return false;
    }

    return $reflection->getConstructor()->isPublic()
        && 1 === $reflection->getConstructor()->getNumberOfRequiredParameters()
        && 'value' === $reflection->getConstructor()->getParameters()[0]->getName();
}

/**
 * @phpstan-template T
 *
 * @phpstan-param  array<mixed, T> $array
 * @phpstan-return array<mixed, T>
 */
function snakeToCamel(array $array): array
{
    foreach ($array as $key => $value) {
        unset($array[$key]);
        $array[is_string($key) ? lcfirst(str_replace(' ', '', ucwords(str_replace('_', ' ', $key)))) : $key] =
            is_array($value) ? snakeToCamel($value) : $value;
    }

    /* @phpstan-ignore-next-line */
    return $array;
}

/**
 * @phpstan-template T
 *
 * @phpstan-param  array<mixed, T> $array
 * @phpstan-return array<mixed, T>
 */
function camelToSnake(array $array): array
{
    foreach ($array as $key => $value) {
        unset($array[$key]);
        $array[is_string($key) ? strtolower(toStr(preg_replace('/[A-Z]/', '_$0', lcfirst($key)))) : $key] =
            is_array($value) ? camelToSnake($value) : $value;
    }

    /* @phpstan-ignore-next-line */
    return $array;
}

/**
 * @phpstan-template T
 *
 * @param array<string, mixed> $x
 * @phpstan-param callable(): T $fn
 * @phpstan-return T
 */
function performance(callable $fn, string $message, array $x = [], int $maxTime = 5, string $level = 'error'): mixed
{
    $start = microtime(true);
    $res = $fn();
    $time = microtime(true) - $start;

    if ($time > $maxTime) {
        Log::log($level, $message, $x + ['execution_time' => "{$time} sec."]);
    }

    return $res;
}

/**
 * @template T
 *
 * @param callable(): T $fn
 *
 * @return T
 */
function retryable(callable $fn, int $max = 5, int $try = 1): mixed
{
    try {
        return $fn();
    } catch (\Throwable $e) {
        if ($try < $max) {
            usleep(100000 ^ $try);

            return retryable($fn, $max, ++$try);
        }
        throw $e;
    }
}

/**
 * @template T
 * @template C
 *
 * @param callable(): T $fn
 * @param callable(): C $compensation
 *
 * @return T
 */
function compensate(callable $fn, callable $compensation): mixed
{
    try {
        return $fn();
    } catch (\Throwable $e) {
        $compensation();
        throw $e;
    }
}

function randomStr(int $length = 10): string
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $characters_len = strlen($characters);
    $random_str = '';
    for ($i = 0; $i < $length; ++$i) {
        $random_str .= $characters[random_int(0, $characters_len - 1)];
    }

    return $random_str;
}

function randomToken(string $salt, int $length = 10): string
{
    return md5(md5(randomStr($length)) . $salt);
}

/**
 * @param array<string, mixed> $array
 *
 * @return array<string, mixed>
 */
function secured(array $array): array
{
    $unsecure = ['md5Key', 'password', 'aesKey'];
    $secured = [];
    foreach ($array as $key => $value) {
        if (!$value || in_array($key, $unsecure, true)) {
            continue;
        }
        $secured[$key] = is_array($value) ? secured($value) : $value;
    }

    return $secured;
}

/** @return array<string, mixed> */
function exceptionContext(Throwable $exception): array
{
    return method_exists($exception, 'context') ? secured($exception->context()) : [];
}

function uuid(): string
{
    $uuid = random_bytes(16);
    $uuid[6] = $uuid[6] & "\x0F" | "\x4F";
    $uuid[8] = $uuid[8] & "\x3F" | "\x80";
    $uuid = bin2hex($uuid);

    return substr($uuid, 0, 8) .
        '-' .
        substr($uuid, 8, 4) . '-' .
        substr($uuid, 12, 4) . '-' .
        substr($uuid, 16, 4) . '-' .
        substr($uuid, 20, 12);
}

function httpError(int $httpStatusCode = 500, int $code = 1, string $msg = 'error'): Response
{
    return new Response(
        status: $httpStatusCode,
        headers: ['Content-Type' => 'application/json'],
        body: toStr(json_encode(['code' => $code, 'msg' => $msg], JSON_UNESCAPED_UNICODE))
    );
}

function ok(mixed $data = null, string $msg = 'ok'): Response
{
    return json([
        'code' => 0,
        'msg' => $msg,
        'data' => $data,
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/**
 * @template T
 *
 * @param class-string<T> $className
 *
 * @return T
 */
function containerGet(string $className): mixed
{
    return Container::get($className);
}

/**
 * @param ?object $val
 *
 * @return ($val is null ? null : string)
 */
function encode(?object $val): ?string
{
    if (null === $val) {
        return null;
    }
    $encoded = json_encode(camelToSnake(marshal($val)));

    if (false === $encoded) {
        throw new \RuntimeException('Failed encoding', 500);
    }

    return $encoded;
}

/**
 * @phpstan-template T of object
 * @phpstan-param class-string<T> $class
 * @phpstan-param string $val
 * @phpstan-return T
 */
function decode(?string $val, string $class): ?object
{
    if (null === $val) {
        return null;
    }
    /** @var array<string, mixed>|false $decoded */
    $decoded = json_decode($val, true);

    if (false === $decoded) {
        throw new \RuntimeException('Failed decoding', 500);
    }

    return unmarshal($class, snakeToCamel($decoded));
}

/**
 * @param non-empty-string $separator
 * @param string[]         $keys
 */
function decodeProps(object $object, array $keys, string $separator = '.'): object
{
    foreach (get_object_vars($object) as $field => $value) {
        if (!is_string($value)) {
            continue;
        }
        $nested = explode($separator, $field);
        foreach ($keys as $key) {
            if (in_array($key, $nested, true)) {
                $object->{$field} = json_decode($value, true);
            }
        }
    }

    return $object;
}

/**
 * @param array<string, callable> $replaces
 */
function nestedReplace(Collection $objects, array $replaces): Collection
{
    $replacements = [];
    foreach ($replaces as $fieldList => $replace) {
        $fields = explode(',', $fieldList);
        $toReplace = [];
        foreach ($objects as $object) {
            foreach ($fields as $field) {
                $field = trim($field);
                if (isset($object->{$field})) {
                    $toReplace[] = $object->{$field};
                }
            }
        }

        $replacement = $replace($toReplace);
        foreach ($fields as $field) {
            $replacements[trim($field)] = $replacement;
        }
    }

    return $objects->map(function ($item) use ($replacements) {
        foreach ($replacements as $field => $replacement) {
            if (isset($item->{$field})) {
                $item->{str_replace('_id', '', $field)} = $replacement[$item->{$field}];
                if ($field !== str_replace('_id', '', $field)) {
                    unset($item->{$field});
                }
            }
        }

        return $item;
    });
}

/**
 * @param array<string, callable> $replaces
 */
function replaceProps(stdClass $object, array $replaces): stdClass
{
    /* @phpstan-ignore-next-line */
    return nestedReplace(collect([$object]), $replaces)->sole();
}

/**
 * @param non-empty-string $separator
 */
function collapseProps(object $data, string $separator = '.'): object
{
    $joined = [];
    foreach (get_object_vars($data) as $field => $value) {
        if (null === $value) {
            continue;
        }
        $keys = explode($separator, $field);
        match (count($keys)) {
            1 => $joined[$keys[0]] = $value,
            /* @phpstan-ignore-next-line */
            2 => $joined[$keys[0]][$keys[1]] = $value,
            /* @phpstan-ignore-next-line */
            3 => $joined[$keys[0]][$keys[1]][$keys[2]] = $value,
            default => throw new \RuntimeException('Collapse props failed because of too many keys'),
        };
    }

    return (object)$joined;
}

/**
 * @param non-empty-string $from
 * @param string[]         $fields
 * @param non-empty-string $separator
 *
 * @return string[]
 */
function selectAs(string $from, string $to, array $fields, string $separator = '.'): array
{
    return array_map(fn ($f) => "{$from}.{$f} as {$to}{$separator}{$f}", $fields);
}

/**
 * @param string[] $left
 * @param string[] $right
 *
 * @return string[]
 */
function selectDiff(array $left, array $right): array
{
    return array_map(fn ($f) => "NULL as {$f}", array_diff($left, $right));
}

function pipe(object $object, callable ...$fn): object
{
    foreach ($fn as $f) {
        $object = $f($object);
    }

    return $object;
}

/** @return array<mixed> */
function jsonDecode(string $json): array
{
    /** @var array<mixed>|false|null $result */
    $result = json_decode($json, true);

    return !is_array($result) ? [] : $result;
}

/** @param array<mixed>|object $data */
function jsonEncode(array|object $data): ?string
{
    $encoded = json_encode($data);

    return false === $encoded ? null : $encoded;
}

function nonEmpty(string $value, string $default): string
{
    return empty($value) ? $default : $value;
}
