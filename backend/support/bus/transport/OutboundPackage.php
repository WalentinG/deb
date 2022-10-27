<?php

declare(strict_types=1);

namespace support\bus\transport;

use support\bus\Metadata;

final class OutboundPackage
{
    public function __construct(
        public readonly string $queue,
        public readonly string|object $payload,
        public readonly Metadata $metadata,
    ) {
    }
}
