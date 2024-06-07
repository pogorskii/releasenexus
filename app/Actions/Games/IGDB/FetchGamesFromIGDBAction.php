<?php

namespace App\Actions\Games\IGDB;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class FetchGamesFromIGDBAction
{
    /**
     * @throws ConnectionException
     */
    public static function execute(int $offsetMultiplier, string $sortingRule = "updated_at desc"): array
    {
        $fields = implode(', ', [
            '*',
            //            'age_ratings.*',
            //            'age_ratings.content_descriptions.*',
            //            'alternative_names.*',
            //            'cover.*',
            //            'game_localizations.*',
            //            'external_games.*',
            //            'language_supports.*',
            //            'release_dates.*',
            //            'screenshots.*',
            //            'videos.*',
            //            'websites.*',
            //            'collection.*',
            //            'collections.*',
            //            'franchise.*',
            //            'franchises.*',
            //            'game_engines.*',
        ]);

        $responses = Http::pool(function (Pool $pool) use ($offsetMultiplier, $fields, $sortingRule) {
            for ($i = 0; $i < 5; $i++) {
                $offsetValue = $i * 500 + $offsetMultiplier * 500;
                $body        = "fields {$fields}; limit 500; offset {$offsetValue}; sort {$sortingRule};";
                $pool->as($i)->igdb()->withBody($body)->post("games");
            }
        });

        $fetchedGames = collect($responses)->map(function (Response|ConnectionException $response) {
            if ($response instanceof ConnectionException) {
                throw new ConnectionException('An error occurred while fetching games from IGDB: '.$response->getMessage());
            }

            return $response->throw()->json();
        })->toArray();

        return Arr::flatten($fetchedGames, 1);
    }
}
