<?php

declare(strict_types=1);

namespace support\bus\annotationsReader\attribute;

final class MethodLevel
{
    /** @param class-string $inClass */
    public function __construct(
        public readonly object $attribute,
        public readonly string $inClass,
        public readonly \ReflectionMethod $reflectionMethod
    ) {
    }
}
