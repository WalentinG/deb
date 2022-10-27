<?php

declare(strict_types=1);

namespace support\bus\serviceHandler\handler;

use support\bus\BusContext;
use support\bus\MessageHandler;

final class StaticMessageHandler implements MessageHandler
{
    public function __construct(private readonly string $className, private readonly string $methodName)
    {
    }

    public function __invoke(object $message, BusContext $x): void
    {
        $this->className::{$this->methodName}($message, $x);
    }
}
