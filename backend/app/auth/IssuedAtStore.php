<?php

declare(strict_types=1);

namespace app\auth;

use support\bootstrap\Redis;

final class IssuedAtStore
{
    private const KEY = 'IM:USER:LAST:IAT:%s';
    private const TTL = 30 * 24 * 60 * 60;

    public function find(string $userId): int
    {
        return (int)Redis::get(sprintf(self::KEY, $userId));
    }

    public function set(string $userId, int $iat): void
    {
        Redis::set(sprintf(self::KEY, $userId), $iat, 'ex', self::TTL);
    }
}
