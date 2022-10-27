<?php

declare(strict_types=1);

namespace support\bus\entrypoint;

use support\bus\transport\IncomingPackage;
use support\bus\transport\Queue;
use support\bus\transport\Transport;

final class EntryPoint
{
    public function __construct(private readonly Transport $transport, private readonly EntrypointProcessor $processor)
    {
    }

    public function listen(Queue ...$queues): void
    {
        $this->transport->consume(
            function (IncomingPackage $package): void {
                $this->processor->process($package);
            },
            ...$queues
        );
    }
}
