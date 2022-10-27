<?php

declare(strict_types=1);

namespace support\auth;

function bearer(string $authorization): string
{
    return trim(substr($authorization, 7));
}
