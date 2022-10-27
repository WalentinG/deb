<?php

declare(strict_types=1);

namespace support\bus\annotationsReader\attribute;

final class ClassLevel
{
    /** @param class-string $inClass */
    public function __construct(public readonly object $attribute, public readonly string $inClass)
    {
    }
}
