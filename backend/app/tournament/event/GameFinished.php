<?php

declare(strict_types=1);

namespace app\tournament\event;

final class GameFinished
{
    public function __construct(
        public readonly string $tournamentId,
        public readonly string $gameId,
        public readonly string $radiantTeamId,
        public readonly string $direTeamId,
        public readonly int $radiantScore,
        public readonly int $direScore,
    ) {
    }
}
