<?php

declare(strict_types=1);

namespace support\bus\transport;

interface Queue
{
    public function name(): string;
}
