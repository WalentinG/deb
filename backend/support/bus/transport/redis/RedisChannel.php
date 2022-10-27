<?php

declare(strict_types=1);

namespace support\bus\transport\redis;

use support\bus\transport\Queue;

final class RedisChannel implements Queue
{
    public function __construct(private readonly string $name)
    {
    }

    public function name(): string
    {
        return $this->name;
    }
}
