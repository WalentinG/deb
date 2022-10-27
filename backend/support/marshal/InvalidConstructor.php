<?php

declare(strict_types=1);

namespace support\marshal;

final class InvalidConstructor extends \RuntimeException
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function notFound(string $class): self
    {
        return new self("Constructor not found in class {$class}");
    }

    public static function notPublic(string $class): self
    {
        return new self("Constructor in class {$class} is not public");
    }
}
