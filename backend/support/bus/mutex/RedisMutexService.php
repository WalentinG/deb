<?php

declare(strict_types=1);

namespace support\bus\mutex;

use support\bootstrap\Redis;

final class RedisMutexService implements MutexService
{
    private const LATENCY_TIMEOUT = 100;
    /** Lock lifetime in seconds */
    private const DEFAULT_LOCK_LIFETIME = 5;

    public function withLock(string $id, callable $code): mixed
    {
        try {
            while (!Redis::set(self::key($id), 'lock', 'EX', self::DEFAULT_LOCK_LIFETIME, 'NX')) {
                usleep(self::LATENCY_TIMEOUT);
            }

            return $code();
        } finally {
            Redis::del(self::key($id));
        }
    }

    private static function key(string $id): string
    {
        return "MUTEX:{$id}";
    }
}
