<?php

declare(strict_types=1);

namespace support\bus;

interface BusContext
{
    public function metadata(): Metadata;

    /** @param array<string, int|float|string|null> $headers */
    public function delivery(object $message, array $headers = []): void;

    /**
     * @param object[]                             $messages
     * @param array<string, int|float|string|null> $headers
     */
    public function deliveryBulk(array $messages, array $headers = []): void;
}
