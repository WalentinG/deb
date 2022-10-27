<?php

declare(strict_types=1);

namespace support\bus;

use support\bus\endpoint\EndpointRouter;

final class KernelContext implements BusContext
{
    public function __construct(
        private readonly EndpointRouter $endpointRouter,
        private readonly Metadata $metadata = new Metadata(),
    ) {
    }

    public function metadata(): Metadata
    {
        return $this->metadata;
    }

    public function delivery(object $message, array $headers = []): void
    {
        foreach ($this->endpointRouter->route($message::class) as $endpoint) {
            $endpoint->delivery($message, $this->metadata->traceId(), $headers);
        }
    }

    public function deliveryBulk(array $messages, array $headers = []): void
    {
        $traceId = $this->metadata->traceId();

        foreach ($messages as $message) {
            foreach ($this->endpointRouter->route($message::class) as $endpoint) {
                $endpoint->delivery($message, $traceId, $headers);
            }
        }
    }
}
