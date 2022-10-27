<?php

declare(strict_types=1);

namespace app\tournament\cmd;

final class CreateTournament
{
    public function __construct(
        public readonly string $tournamentId,
        public readonly string $name
    ) {
    }
}
