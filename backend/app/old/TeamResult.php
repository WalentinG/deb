<?php

declare(strict_types=1);

namespace app\old;

final class TeamResult
{
    public string $name;
    public float  $points = 0;
    public int    $wins   = 0;
    public int    $score  = 0;
    public int    $games  = 0;
    public array  $competitors  = [];

    public function __construct(Team $team)
    {
        $this->name = $team->name;
    }

    public function compare(TeamResult $team): int
    {
        if ($this->points !== $team->points) {
            return $this->points <=> $team->points;
        }
        if ($this->wins !== $team->wins) {
            return $this->wins <=> $team->wins;
        }
//        if ($this->score === $team->score) {
//            return $this->berger() <=> $team->berger();
//        }
        if ($this->personal($team)) {
            return $this->personal($team);
        }
        return $this->score <=> $team->score;

    }

    public function win(TeamResult $team, int $winner, int $loser): void
    {
        $this->wins++;
        $this->games++;
        $this->score += $winner;
        $this->points += $loser > 500 ? 1 : 1.5;
        $this->competitors[] = $team;

        $team->games++;
        $team->score += $loser;
    }

    public function berger(): float
    {
        return array_sum(array_map(fn (TeamResult $t) => $t->points, $this->competitors));
    }

    public function personal(TeamResult $team): int
    {
        return in_array($team, $this->competitors) <=> in_array($this, $team->competitors);
    }
}
