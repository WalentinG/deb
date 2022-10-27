<?php

declare(strict_types=1);

namespace app\old;

final class Tournament
{
    public array $games = [];

    public function add(Team $t1, Team $t2): Tournament
    {
        if ($t1->score > $t2->score) {
            $this->results($t1)->win($this->results($t2), $t1->score, $t2->score);
        } else {
            $this->results($t2)->win($this->results($t1), $t2->score, $t1->score);
        }

        return $this;
    }

    public function dump(): \Generator
    {
        $games = $this->games;
        usort($games, fn (TeamResult $a, TeamResult $b) => $b->compare($a));

        foreach($games as $game) {
            yield [$game->name, $game->points, $game->wins, $game->score, $game->games];
        }
    }

    private function results(Team $t): TeamResult
    {
        return $this->games[get_class($t)] ?? $this->games[get_class($t)] = new TeamResult($t);
    }
}
