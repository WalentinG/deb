<?php

declare(strict_types=1);

namespace tests\unit\support\authorization;

use support\auth\Token;

final class TokenStub extends Token
{
    public function __construct(public string $a, public string $b, public int $iat, public int $exp)
    {
    }
}
