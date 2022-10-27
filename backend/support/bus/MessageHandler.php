<?php

declare(strict_types=1);

namespace support\bus;

interface MessageHandler
{
    public function __invoke(object $message, BusContext $x): void;
}
