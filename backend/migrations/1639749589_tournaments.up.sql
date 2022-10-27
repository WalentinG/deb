CREATE TABLE IF NOT EXISTS tournaments (
    `id` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `created_at` timestamp default current_timestamp() NOT NULL,
    `started_at` timestamp NULL,
    `finished_at` timestamp NULL,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS teams (
    `id` varchar(255) NOT NULL,
    `name` varchar(255) NOT NULL,
    `created_at` timestamp default current_timestamp() NOT NULL,
    PRIMARY KEY (id)
);

CREATE TABLE IF NOT EXISTS tournament_teams (
    `tournament_id` varchar(255) NOT NULL,
    `team_id` varchar(255) NOT NULL,
    `registred_at` timestamp default current_timestamp() NOT NULL,
    PRIMARY KEY (tournament_id, team_id),
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id),
    FOREIGN KEY (team_id) REFERENCES teams(id)
);

CREATE TABLE IF NOT EXISTS games (
    `id` varchar(255) NOT NULL,
    `round` int(255) NOT NULL,
    `tournament_id` varchar(255) NOT NULL,
    `dire_team_id` varchar(255) NOT NULL,
    `dire_score` int NULL,
    `radiant_score` int NULL,
    `radiant_team_id` varchar(255) NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id),
    FOREIGN KEY (dire_team_id) REFERENCES teams(id),
    FOREIGN KEY (radiant_team_id) REFERENCES teams(id)
);

CREATE TABLE IF NOT EXISTS standings (
    `id` int AUTO_INCREMENT,
    `tournament_id` varchar(255) NOT NULL,
    `team_id` varchar(255) NOT NULL,
    `score` int NULL,
    `opponent_score` int NULL,
    `opponent_team_id` varchar(255) NOT NULL,
    `created_at` timestamp default current_timestamp() NOT NULL,
    PRIMARY KEY (id),
    FOREIGN KEY (tournament_id) REFERENCES tournaments(id),
    FOREIGN KEY (team_id) REFERENCES teams(id),
    FOREIGN KEY (opponent_team_id) REFERENCES teams(id)
);
