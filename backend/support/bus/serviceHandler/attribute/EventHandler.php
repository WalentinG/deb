<?php

declare(strict_types=1);

namespace support\bus\serviceHandler\attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]

final class EventHandler
{
    public function __construct()
    {
    }
}
