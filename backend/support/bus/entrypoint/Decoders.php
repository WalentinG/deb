<?php

declare(strict_types=1);

namespace support\bus\entrypoint;

final class Decoders
{
    /** @param array<string, Decoder> $decoders */
    public function __construct(private readonly array $decoders)
    {
    }

    public function get(string $contentType): Decoder
    {
        foreach ($this->decoders as $decoder) {
            if ($decoder->supports($contentType)) {
                return $decoder;
            }
        }
        throw new \RuntimeException(sprintf('No decoder found for %s', $contentType));
    }
}
