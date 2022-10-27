<?php

declare(strict_types=1);

namespace app\old;

final class Team
{
    public function __construct(
        public readonly int $score = 0,
        public readonly string $name = 'team'
    ) {
    }
}
