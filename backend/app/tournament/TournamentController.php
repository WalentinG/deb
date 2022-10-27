<?php

declare(strict_types=1);

namespace app\tournament;

use app\tournament\cmd\FinishGame;
use app\tournament\cmd\CreateTournament;
use app\tournament\cmd\RegisterTeam;
use app\tournament\cmd\StartTournament;
use support\bus\KernelContext;
use support\Request;
use support\Response;

final class TournamentController
{
    public function __construct(private readonly KernelContext $x)
    {
    }

    public function create(Request $request): Response
    {
        $this->x->delivery($request->unmarshal(CreateTournament::class, ['tournamentId' => uuid()]));

        return ok();
    }

    public function registerTeam(Request $request): Response
    {
        $this->x->delivery($request->unmarshal(RegisterTeam::class, ['tournamentId' => Tournament::id()]));

        return ok();
    }

    public function start(Request $request): Response
    {
        $this->x->delivery($request->unmarshal(StartTournament::class, ['tournamentId' => Tournament::id()]));

        return ok();
    }

    public function rounds(): Response
    {
        $rounds = [];
        foreach (Game::games(Tournament::id())->groupBy('round') as $round => $roundGames) {
            $rounds[] = ['id' => $round, 'games' => $roundGames];
        }

        return ok($rounds);
    }

    public function finishGame(Request $request): Response
    {
        $this->x->delivery($request->unmarshal(FinishGame::class, ['tournamentId' => Tournament::id()]));

        return ok();
    }

    public function standings(): Response
    {
        return ok(Standings::all(Tournament::id()));
    }
}
