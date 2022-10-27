<?php

declare(strict_types=1);

namespace support\bus\messagesRouter\exception;

final class InvalidEventClassSpecified extends \LogicException
{
    public static function wrongEventClass(): self
    {
        return new self('The event class is not specified, or does not exist');
    }
}
