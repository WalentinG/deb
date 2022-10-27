<?php

declare(strict_types=1);

namespace support\bus\endpoint;

use support\bus\Metadata;
use support\bus\transport\OutboundPackage;
use support\bus\transport\Transport;

final class Endpoint
{
    public function __construct(public readonly string $name, private readonly Transport $transport, private readonly Encoder $encoder)
    {
    }

    /**
     * @param array<string, int|float|string|null> $headers
     */
    public function delivery(object $message, string $traceId, array $headers): void
    {
        $this->transport->send($this->createOutboundPackage($message, $traceId, new Metadata($headers)));
    }

    /**
     * @param object[]                             $messages
     * @param array<string, int|float|string|null> $headers
     */
    public function deliveryBulk(array $messages, string $traceId, array $headers): void
    {
        $this->transport->send(
            ...array_map(fn (object $message) => $this->createOutboundPackage($message, $traceId, new Metadata($headers)), $messages)
        );
    }

    private function createOutboundPackage(object $message, string $traceId, Metadata $metadata): OutboundPackage
    {
        return new OutboundPackage(
            queue: $this->name,
            payload: $this->encoder->encode($message),
            metadata: $metadata->with($traceId, uuid(), $this->encoder->messageType($message), $this->encoder->contentType()),
        );
    }
}
