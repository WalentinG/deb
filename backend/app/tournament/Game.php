<?php

declare(strict_types=1);

namespace app\tournament;

use app\tournament\cmd\FinishGame;
use app\tournament\event\GameFinished;
use app\tournament\event\TournamentStarted;
use Illuminate\Support\Collection;
use support\bus\BusContext;
use support\bus\serviceHandler\attribute\CommandHandler;
use support\bus\serviceHandler\attribute\EventHandler;
use support\Db;

final class Game
{
    #[EventHandler]
    public static function whenTournamentStarted(TournamentStarted $event): void
    {
        $rounds = [];
        foreach (schedule(Tournament::teams($event->tournamentId)) as $id => $games) {
            foreach ($games as [$radiant, $dire]) {
                $rounds[] = [
                    'id' => uuid(),
                    'round' => $id,
                    'tournament_id' => $event->tournamentId,
                    'dire_team_id' => $radiant,
                    'radiant_team_id' => $dire,
                ];
            }
        }

        Db::table('games')->insert($rounds);
    }

    #[CommandHandler]
    public static function finishGame(FinishGame $cmd, BusContext $x): void
    {
        Db::table('games')
            ->where('id', $cmd->gameId)
            ->update([
                'radiant_score' => $cmd->radiantScore,
                'dire_score' => $cmd->direScore,
            ]);

        $game = Db::table('games')
            ->where('id', $cmd->gameId)
            ->select('radiant_team_id', 'dire_team_id')
            ->sole();

        $x->delivery(
            new GameFinished(
                tournamentId: $cmd->tournamentId,
                gameId: $cmd->gameId,
                radiantTeamId: $game->radiant_team_id,
                direTeamId: $game->dire_team_id,
                radiantScore: $cmd->radiantScore,
                direScore: $cmd->direScore
            )
        );
    }

    public static function games(string $tournamentId): Collection
    {
        return Db::table('games as g')
            ->select(array_merge(
                ['g.id', 'g.round', 'g.dire_score', 'g.radiant_score'],
                selectAs('d', 'dire_team', ['id', 'name']),
                selectAs('r', 'radiant_team', ['id', 'name']),
            ))
            ->join('teams as r', 'r.id', '=', 'g.radiant_team_id')
            ->join('teams as d', 'd.id', '=', 'g.dire_team_id')
            ->where('tournament_id', $tournamentId)
            ->orderBy('g.round')
            ->get()
            ->map(fn ($i) => collapseProps($i));
    }
}
