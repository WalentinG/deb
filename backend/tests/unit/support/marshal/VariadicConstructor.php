<?php

declare(strict_types=1);

namespace tests\unit\support\marshal;

final class VariadicConstructor
{
    /** @var SomeValue[] */
    public readonly array $values;

    public function __construct(SomeValue ...$values)
    {
        $this->values = $values;
    }
}
