<?php

declare(strict_types=1);

namespace app\tournament;

use app\tournament\cmd\CreateTournament;
use app\tournament\cmd\RegisterTeam;
use app\tournament\cmd\StartTournament;
use app\tournament\event\TournamentStarted;
use support\bus\BusContext;
use support\bus\serviceHandler\attribute\CommandHandler;
use support\Db;

final class Tournament
{
    #[CommandHandler]
    public static function create(CreateTournament $cmd): void
    {
        Db::table('tournaments')->insert(['id' => $cmd->tournamentId, 'name' => $cmd->name]);
    }

    #[CommandHandler]
    public static function registerTeam(RegisterTeam $cmd): void
    {
        Db::table('tournament_teams')->insert(['tournament_id' => $cmd->tournamentId, 'team_id' => $cmd->teamId,]);
    }

    #[CommandHandler]
    public static function start(StartTournament $cmd, BusContext $x): void
    {
        Db::table('tournaments')
            ->where('id', $cmd->tournamentId)
            ->update(['started_at' => date('Y-m-d H:i:s')]);

        $x->delivery(new TournamentStarted($cmd->tournamentId));
    }

    public static function id(): string
    {
        return Db::table('tournaments')
            ->whereNull('finished_at')
            ->value('id') ?? throw new \InvalidArgumentException('Tournament not found');
    }

    /** @return string[] */
    public static function teams(string $tournamentId): array
    {
        return Db::table('tournament_teams')
            ->select('team_id')
            ->where('tournament_id', $tournamentId)
            ->pluck('team_id')
            ->toArray();
    }
}
