<?php

namespace App\Actions\Games;

use App\Actions\Games\IGDB\FetchGamesFromIGDBAction;
use Exception;
use Illuminate\Process\Pool;
use Illuminate\Support\Facades\Process;

class TransitGamesFromIGDBToDB
{
    public static function execute(int $iterations = 10): array
    {
        try {
            $games = [];

            $pool = Process::pool(function (Pool $pool) use ($iterations) {
                for ($i = 0; $i < $iterations; $i++) {
                    $pool->as($i)->command('php artisan games:dump');
                }
            })->start();

            $games = $pool->wait();

            $mergedGames = $games[0]->output();

            dd($mergedGames);

//            for ($i = 0; $i < $iterations; $i++) {
//                $games = array_merge($games, FetchGamesFromIGDB::execute($i));
//            }

//            $games  = FetchGamesFromIGDB::execute();
            $result = AddGamesToDBAction::execute($mergedGames);

            return $result;
        } catch (Exception $e) {
            // Log error
        }
    }
}
