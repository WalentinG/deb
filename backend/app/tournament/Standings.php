<?php

declare(strict_types=1);

namespace app\tournament;

use app\tournament\event\GameFinished;
use Illuminate\Support\Collection;
use support\bus\serviceHandler\attribute\EventHandler;
use support\Db;

final class Standings
{
    #[EventHandler]
    public static function whenGameFinished(GameFinished $event): void
    {
        Db::table('standings')->insert([
            [
                'tournament_id' => $event->tournamentId,
                'team_id' => $event->radiantTeamId,
                'score' => $event->radiantScore,
                'opponent_team_id' => $event->direTeamId,
                'opponent_score' => $event->direScore,
            ],
            [
                'tournament_id' => $event->tournamentId,
                'team_id' => $event->direTeamId,
                'score' => $event->direScore,
                'opponent_team_id' => $event->radiantTeamId,
                'opponent_score' => $event->radiantScore,
            ],
        ]);
    }

    public static function all(string $tournamentId): Collection
    {
        return Db::table('standings')
            ->select(Db::raw(<<<'SQL'
            	teams.id as 'team.id',
             	teams.name as 'team.name',
                sum(case when score - opponent_score > 500 THEN 1.5 WHEN score > opponent_score THEN 1 ELSE 0 END)as points,
                sum(case when score > opponent_score THEN 1 ELSE 0 END) as wins,
                sum(score) as score,
                count(standings.id) as games
            SQL))
            ->join('teams', 'teams.id', '=', 'team_id')
            ->where('tournament_id', $tournamentId)
            ->groupBy('teams.id', 'teams.name')
            ->orderByDesc('points')
            ->orderByDesc('wins')
            ->orderByDesc('score')
            ->get()
            ->map(fn ($i) => collapseProps($i));
    }
}
