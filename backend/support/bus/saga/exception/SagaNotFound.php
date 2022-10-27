<?php

declare(strict_types=1);

namespace support\bus\saga\exception;

final class SagaNotFound extends \RuntimeException
{
    /** @psalm-param class-string $sagaClass */
    public function __construct(string $sagaClass)
    {
        parent::__construct("{$sagaClass} not found by criteria");
    }
}
