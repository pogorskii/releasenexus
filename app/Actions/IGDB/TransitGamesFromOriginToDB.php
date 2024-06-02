<?php

namespace App\Actions\IGDB;

class TransitGamesFromOriginToDB
{
    public static function execute(): \Closure
    {
        try {
            $games = \App\Actions\IGDB\FetchGames::execute();
            \App\Actions\IGDB\SaveGamesToDB::execute($games);

            return function ($bar) use ($games) {
                $bar->start(count($games));
                foreach ($games as $game) {
                    $bar->advance();
                }
                $bar->finish();
            };
        } catch (\Exception $e) {
            // Log error
        }
    }
}
