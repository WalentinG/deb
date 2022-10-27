<?php

declare(strict_types=1);

namespace support\bus\serviceHandler\handler;

use Psr\Container\ContainerInterface;
use support\bus\BusContext;
use support\bus\MessageHandler;

final class InstanceMessageHandler implements MessageHandler
{
    public function __construct(private readonly ContainerInterface $container, readonly string $className, private readonly string $methodName)
    {
    }

    public function __invoke(object $message, BusContext $x): void
    {
        $this->container->get($this->className)->{$this->methodName}($message, $x);
    }
}
