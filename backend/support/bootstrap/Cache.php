<?php

declare(strict_types=1);

namespace support\bootstrap;

use Closure;
use Illuminate\Cache\RedisStore;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Redis\RedisManager;
use support\cache\NoStaleData;
use Webman\Bootstrap;
use Workerman\Worker;

/**
 * @method static mixed remember(string $key, int $ttl, Closure $callback)
 * @method static Lock lock(string $name, int $seconds = 0, mixed $owner = null)
 */
final class Cache implements Bootstrap
{
    private const STALE_KEY = 'STALE:%s';
    private static Repository $_instance;

    /**
     * @param Worker $worker
     */
    public static function start($worker): void
    {
        $config = config('redis');

        $rs = new RedisStore(new RedisManager('', 'phpredis', $config));
        self::$_instance = new Repository($rs);
    }

    public static function __callStatic(string $name, mixed $arguments): mixed
    {
        return self::$_instance->{$name}(...$arguments);
    }

    /**
     * @template T
     *
     * @param callable(): T $fn
     *
     * @return T
     */
    public static function immortal(string $key, int $ttl, callable $fn): mixed
    {
        if ($cached = Redis::get($key)) {
            /* @phpstan-ignore-next-line */
            return json_decode(toStr($cached), true);
        }

        $resp = self::lock('LOCK:' . $key, $ttl)->get($fn);
        if (!$resp) {
            /* @phpstan-ignore-next-line */
            return json_decode(toStr(Redis::get(sprintf(self::STALE_KEY, $key))), true) ?? throw new NoStaleData();
        }

        $data = toStr(json_encode($resp, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        Redis::set($key, $data, 'ex', $ttl);
        Redis::set(sprintf(self::STALE_KEY, $key), $data);
        /* @phpstan-ignore-next-line */
        return json_decode($data, true);
    }
}
