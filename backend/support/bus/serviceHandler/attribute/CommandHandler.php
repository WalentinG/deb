<?php

declare(strict_types=1);

namespace support\bus\serviceHandler\attribute;

/**
 * @psalm-immutable
 */
#[\Attribute(\Attribute::TARGET_METHOD)]
final class CommandHandler
{
    public function __construct()
    {
    }
}
