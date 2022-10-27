<?php

declare(strict_types=1);

namespace support\bus\saga\attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
final class SagaEventHandler
{
    public function __construct()
    {
    }
}
