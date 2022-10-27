<?php

declare(strict_types=1);

namespace app\tournament\cmd;

final class RegisterTeam
{
    public function __construct(
        public readonly string $tournamentId,
        public readonly string $teamId
    ) {
    }
}
