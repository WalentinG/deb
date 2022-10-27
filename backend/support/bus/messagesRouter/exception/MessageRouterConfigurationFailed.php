<?php

declare(strict_types=1);

namespace support\bus\messagesRouter\exception;

final class MessageRouterConfigurationFailed extends \RuntimeException
{
    public static function fromThrowable(\Throwable $throwable): self
    {
        return new self($throwable->getMessage(), $throwable->getCode(), $throwable);
    }
}
