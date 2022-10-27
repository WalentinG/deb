<?php

declare(strict_types=1);

namespace support\bootstrap;

use support\bus\entrypoint\EntryPoint;
use support\bus\entrypoint\EntrypointProcessor;
use support\bus\transport\imMemory\InMemoryTransport;
use support\bus\transport\redis\RedisChannel;
use support\bus\transport\redis\RedisTransport;
use Webman\Bootstrap;
use Workerman\Worker;

final class Subscriber implements Bootstrap
{
    /** @param Worker $worker */
    public static function start($worker): void
    {
//        (new EntryPoint(
//            transport: containerGet(RedisTransport::class),
//            processor: containerGet(EntrypointProcessor::class)
//        ))->listen(containerGet(RedisChannel::class));

        (new EntryPoint(
            transport: containerGet(InMemoryTransport::class),
            processor: containerGet(EntrypointProcessor::class)
        ))->listen();
    }
}
