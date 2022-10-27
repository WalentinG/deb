<?php

declare(strict_types=1);

namespace tests\unit\support\marshal;

final class PublicConstructor
{
    public function __construct(
        public string $string,
        public int $int,
        public EnumStub $enum,
        public SomeValue $value,
        public ?float $float = null
    ) {
    }
}
