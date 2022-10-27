<?php

declare(strict_types=1);

namespace support\bus\transport\redis;

use support\bus\Metadata;
use support\bus\transport\IncomingPackage;
use support\bus\transport\OutboundPackage;
use support\bus\transport\Queue;
use support\bus\transport\Transport;
use Workerman\RedisQueue\Client;

final class RedisTransport implements Transport
{
    public function __construct(private readonly Client $client)
    {
    }

    public function send(OutboundPackage ...$packages): void
    {
        foreach ($packages as $package) {
            $this->client->send($package->queue, [$package->payload, $package->metadata->headers]);
        }
    }

    public function consume(callable $onMessage, Queue ...$queues): void
    {
        foreach ($queues as $queue) {
            $this->client->subscribe($queue->name(), function ($data) use ($onMessage): void {
                [$payload, $headers] = $data;
                $onMessage(new IncomingPackage($payload, new Metadata($headers)));
            });
        }
    }
}
