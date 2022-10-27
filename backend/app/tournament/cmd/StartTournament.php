<?php

declare(strict_types=1);

namespace app\tournament\cmd;

final class StartTournament
{
    public function __construct(
        public readonly string $tournamentId,
    ) {
    }
}
