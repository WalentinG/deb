<?php

declare(strict_types=1);

namespace support\auth;

final class PrivateKey
{
    public function __construct(public string $key, public string $algorithm)
    {
    }

    public static function ES256(string $base64Encoded): self
    {
        return new self(toStr(base64_decode($base64Encoded, true)), 'ES256');
    }

    public static function HS256(string $key): self
    {
        return new self($key, 'HS256');
    }
}
