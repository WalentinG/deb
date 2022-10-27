<?php

declare(strict_types=1);

namespace support\auth;

final class JwtTokenException extends \InvalidArgumentException
{
    public static function invalid(): self
    {
        return new self('_INVALID_TOKEN_', 4011);
    }

    public static function revoked(): self
    {
        return new self('_INVALID_TOKEN_', 4012);
    }

    // 如果存储签发时间大于当前时间,表示被拉黑
    public static function blocked(): self
    {
        return new self('_INVALID_TOKEN_', 4013);
    }

    public static function unexpected(\InvalidArgumentException|\UnexpectedValueException $e): self
    {
        return new self('_INVALID_TOKEN_', 4014, $e);
    }

    public static function undefined(): self
    {
        return new self('_INVALID_TOKEN_', 4015);
    }

    public static function unstated(): self
    {
        return new self('_INVALID_TOKEN_', 4016);
    }

    public static function invalidPrivateKey(): self
    {
        return new self('_INVALID_TOKEN_', 4017);
    }
}
