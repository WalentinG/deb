<?php

declare(strict_types=1);

namespace support\bus\saga\attribute;

#[\Attribute(\Attribute::TARGET_METHOD)]
final class SagaCommandHandler
{
    public function __construct()
    {
    }
}
