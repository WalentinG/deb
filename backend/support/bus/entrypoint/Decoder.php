<?php

declare(strict_types=1);

namespace support\bus\entrypoint;

interface Decoder
{
    public function decode(string|object $message, string $className): object;

    public function supports(string $contentType): bool;
}
