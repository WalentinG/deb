<?php

declare(strict_types=1);

namespace app\tournament\cmd;

final class FinishGame
{
    public function __construct(
        public readonly string $tournamentId,
        public readonly string $gameId,
        public readonly int $radiantScore,
        public readonly int $direScore,
    ) {
    }
}
