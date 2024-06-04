<?php

namespace App\Actions\Games\IGDB;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class FetchGamesFromIGDBAction
{
    /**
     * @throws ConnectionException
     */
    public static function execute(int $iterations = 1, string $sortingRule = "updated_at desc"): array
    {
        try {
            $fields = implode(', ', [
                '*',
                'age_ratings.*',
                'age_ratings.content_descriptions.*',
                'alternative_names.*',
                'cover.*',
                'game_localizations.*',
                'external_games.*',
                'language_supports.*',
                'release_dates.*',
                'screenshots.*',
                'videos.*',
                'websites.*',
                'collection.*',
                'collections.*',
                'franchise.*',
                'franchises.*',
                'game_engines.*',
            ]);

            $responses = Http::pool(function (Pool $pool) use ($iterations, $fields, $sortingRule) {
                for ($i = 0; $i < $iterations; $i++) {
                    $offsetValue = $i * 500;
                    $body        = "fields {$fields}; where themes != (42); limit 500; offset {$offsetValue}; sort {$sortingRule};";
                    $pool->as($i)->igdb()->withBody($body)->post("games");
                }
            });

            $fetchedGames = [];
            foreach ($responses as $response) {
                if (gettype($response) === 'object' && $response instanceof Response && $response->successful()) {
                    $fetchedGames = array_merge($fetchedGames, $response->json());
                } else {
                    throw $response;
                }
            }

            return $fetchedGames;
        } catch (RequestException $e) {
            throw new ConnectionException('An error occurred while fetching games fromdfsfsfsf IGDB: '.$e->getMessage());
        }
    }
}
