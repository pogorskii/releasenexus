<?php

namespace App\Actions\Games\IGDB;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class FetchGamesFromIGDB
{
    /**
     * @throws ConnectionException
     */
    public static function execute(string $sortingRule = "updated_at desc", int $offset = 0): array
    {
        $fields       = [
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
        ];
        $fieldsString = implode(', ', $fields);

        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Client-ID'     => config('services.igdb.client_id'),
            'Authorization' => 'Bearer '.config('services.igdb.access_token'),
        ])->withBody("fields {$fieldsString}; where themes != (42); limit 500; offset {$offset}; sort {$sortingRule};")->post("https://api.igdb.com/v4/games");

        return $response->json();
    }
}
