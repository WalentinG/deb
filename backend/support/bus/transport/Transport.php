<?php

declare(strict_types=1);

namespace support\bus\transport;

interface Transport
{
    public function send(OutboundPackage ...$packages): void;

    /** @param callable(IncomingPackage): void $onMessage */
    public function consume(callable $onMessage, Queue ...$queues): void;
}
