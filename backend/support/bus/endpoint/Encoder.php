<?php

declare(strict_types=1);

namespace support\bus\endpoint;

interface Encoder
{
    public function encode(object $message): string|object;

    public static function contentType(): string;

    public function messageType(object $message): string;
}
