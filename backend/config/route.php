<?php

declare(strict_types=1);

namespace config;

use app\tournament\TournamentController;
use Webman\Route;

Route::any('/', fn () => response('ok'));

Route::group('', function (): void {
    Route::post('/createTournament', [TournamentController::class, 'create']);
    Route::post('/registerTeam', [TournamentController::class, 'registerTeam']);
    Route::post('/startTournament', [TournamentController::class, 'start']);
    Route::get('/getRounds', [TournamentController::class, 'rounds']);
    Route::post('/finishGame', [TournamentController::class, 'finishGame']);
    Route::get('/getStandings', [TournamentController::class, 'standings']);
});

Route::fallback(function () {
    return response(
        json_encode(['code' => 404, 'msg' => '404 not found'], JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
        404,
        ['Content-Type' => 'application/json']
    );
});
