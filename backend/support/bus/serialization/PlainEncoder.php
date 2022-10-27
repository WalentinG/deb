<?php

declare(strict_types=1);

namespace support\bus\serialization;

use support\bus\endpoint\Encoder;
use support\bus\entrypoint\Decoder;

final class PlainEncoder implements Encoder, Decoder
{
    public function encode(object $message): object
    {
        return $message;
    }

    public function decode(string|object $message, string $className): object
    {
        if (\is_string($message)) {
            throw new \RuntimeException('PlainEncoder can only decode objects');
        }

        return $message;
    }

    public static function contentType(): string
    {
        return 'application/php-object';
    }

    public function messageType(object $message): string
    {
        return $message::class;
    }

    public function supports(string $contentType): bool
    {
        return 'application/php-object' === $contentType;
    }
}
