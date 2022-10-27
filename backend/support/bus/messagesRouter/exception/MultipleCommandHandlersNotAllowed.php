<?php

declare(strict_types=1);

namespace support\bus\messagesRouter\exception;

final class MultipleCommandHandlersNotAllowed extends \LogicException
{
    public static function duplicate(string $commandClass): self
    {
        return new self(sprintf('A handler has already been registered for the "%s" command', $commandClass));
    }
}
