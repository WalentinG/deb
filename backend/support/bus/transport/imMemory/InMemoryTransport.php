<?php

declare(strict_types=1);

namespace support\bus\transport\imMemory;

use support\bus\transport\IncomingPackage;
use support\bus\transport\OutboundPackage;
use support\bus\transport\Queue;
use support\bus\transport\Transport;

final class InMemoryTransport implements Transport
{
    /** @var ?callable(IncomingPackage): void */
    private $onMessage;

    public function send(OutboundPackage ...$packages): void
    {
        if (!$this->onMessage) {
            return;
        }
        foreach ($packages as $package) {
            ($this->onMessage)(new IncomingPackage($package->payload, $package->metadata));
        }
    }

    public function consume(callable $onMessage, Queue ...$queues): void
    {
        $this->onMessage = $onMessage;
    }
}
