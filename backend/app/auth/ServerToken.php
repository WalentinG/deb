<?php

declare(strict_types=1);

namespace app\auth;

final class ServerToken
{
    public function __construct(public readonly string $value)
    {
    }
}
