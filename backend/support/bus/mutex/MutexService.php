<?php

declare(strict_types=1);

namespace support\bus\mutex;

interface MutexService
{
    /**
     * @template T
     *
     * @psalm-param non-empty-string $id
     * @psalm-param callable(): T $code
     *
     * @return T
     */
    public function withLock(string $id, callable $code): mixed;
}
