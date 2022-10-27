<?php

declare(strict_types=1);

namespace support\bus\messagesRouter\exception;

final class InvalidCommandClassSpecified extends \LogicException
{
    public static function wrongCommandClass(): self
    {
        return new self('The command class is not specified, or does not exist');
    }
}
