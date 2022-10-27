<?php

declare(strict_types=1);

namespace app\tournament\event;

final class TournamentStarted
{
    public function __construct(public readonly string $tournamentId)
    {
    }
}
