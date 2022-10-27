<?php

declare(strict_types=1);

namespace support\crypto;

final class ButterException extends \InvalidArgumentException
{
    public static function opensslDecryptionFailed(): self
    {
        return new self('Decryption failed');
    }

    public static function jsonDecodingFailed(\Throwable $e): self
    {
        return new self(message: $e->getMessage(), previous: $e);
    }

    public static function notFoundInRequest(): self
    {
        return new self('Butter was not found in the request');
    }
}
