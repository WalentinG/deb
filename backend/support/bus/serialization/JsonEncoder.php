<?php

declare(strict_types=1);

namespace support\bus\serialization;

use support\bus\endpoint\Encoder;
use support\bus\entrypoint\Decoder;

final class JsonEncoder implements Encoder, Decoder
{
    public function encode(object $message): string
    {
        return toStr(json_encode(marshal($message)));
    }

    public function decode(string|object $message, string $className): object
    {
        if (\is_object($message)) {
            throw new \RuntimeException('JsonEncoder can only decode strings');
        }

        if (!class_exists($className)) {
            throw new \RuntimeException("Class {$className} does not exist");
        }

        /** @var array<string, mixed> $data */
        $data = json_decode($message, true);

        return unmarshal($className, $data);
    }

    public static function contentType(): string
    {
        return 'application/json';
    }

    public function messageType(object $message): string
    {
        return $message::class;
    }

    public function supports(string $contentType): bool
    {
        return 'application/json' === $contentType;
    }
}
