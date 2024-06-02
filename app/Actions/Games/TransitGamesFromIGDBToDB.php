<?php

namespace App\Actions\Games;

use App\Actions\Games\IGDB\FetchGamesFromIGDB;
use Exception;

class TransitGamesFromIGDBToDB
{
    public static function execute(): array
    {
        try {
            $games  = FetchGamesFromIGDB::execute();
            $result = AddGamesToDB::execute($games);

            return $result;
        } catch (Exception $e) {
            // Log error
        }
    }
}
