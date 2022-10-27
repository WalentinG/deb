<?php

declare(strict_types=1);

namespace tests\unit\support\marshal;

final class PrivateConstructorStub
{
    private function __construct(public string $string, public int $int, public float $float)
    {
    }
}
