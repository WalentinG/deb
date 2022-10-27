<?php

declare(strict_types=1);

namespace support\bus;

use support\bus\endpoint\EndpointRouter;
use support\bus\transport\IncomingPackage;

final class ContextFactory
{
    public function __construct(private readonly EndpointRouter $router)
    {
    }

    public function create(IncomingPackage $package): BusContext
    {
        return new KernelContext($this->router, $package->metadata);
    }
}
