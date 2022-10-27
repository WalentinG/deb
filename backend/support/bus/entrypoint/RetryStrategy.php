<?php

declare(strict_types=1);

namespace support\bus\entrypoint;

use support\bus\BusContext;
use support\bus\MessageHandler;

interface RetryStrategy
{
    public function retry(object $message, BusContext $x, MessageHandler $executor): void;
}
