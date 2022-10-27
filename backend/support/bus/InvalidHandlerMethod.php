<?php

declare(strict_types=1);

namespace support\bus;

final class InvalidHandlerMethod extends \LogicException
{
    public static function tooManyArguments(\ReflectionMethod $reflectionMethod): self
    {
        return new self(
            sprintf(
                'There are too many arguments for the "%s:%s" method. A handler can only accept an argument: the class of the message he handles',
                $reflectionMethod->getDeclaringClass()->getName(),
                $reflectionMethod->getName()
            )
        );
    }

    public static function wrongEventArgument(\ReflectionMethod $reflectionMethod): self
    {
        return new self(
            sprintf(
                'The command handler "%s:%s" should take as the first argument an object',
                $reflectionMethod->getDeclaringClass()->getName(),
                $reflectionMethod->getName()
            )
        );
    }

    public static function wrongVisibility(\ReflectionMethod $reflectionMethod): self
    {
        return new self(
            sprintf(
                'The command handler "%s:%s" should have public visibility.',
                $reflectionMethod->getDeclaringClass()->getName(),
                $reflectionMethod->getName()
            )
        );
    }
}
