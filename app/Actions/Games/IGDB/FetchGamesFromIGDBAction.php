<?php

namespace App\Actions\Games\IGDB;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class FetchGamesFromIGDBAction
{
    /**
     * @throws ConnectionException
     */
    public static function execute(int $iterations = 1, string $sortingRule = "updated_at desc"): array
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

//        $offsetValue = $offset * 500;

        $headers = [
            'Accept'        => 'application/json',
            'Client-ID'     => config('services.igdb.client_id'),
            'Authorization' => 'Bearer '.config('services.igdb.access_token'),
        ];
//        $body    = "fields {$fieldsString}; where themes != (42); limit 500; offset {$offsetValue}; sort {$sortingRule};";

//        $response = Http::withHeaders([
//            'Accept'        => 'application/json',
//            'Client-ID'     => config('services.igdb.client_id'),
//            'Authorization' => 'Bearer '.config('services.igdb.access_token'),
//        ])->withBody("fields {$fieldsString}; where themes != (42); limit 500; offset {$offsetValue}; sort {$sortingRule};")->post("https://api.igdb.com/v4/games");

        $responses = Http::pool(function (Pool $pool) use ($iterations, $headers, $fieldsString, $sortingRule) {
            for ($i = 0; $i < $iterations; $i++) {
                $offsetValue = $i * 500;
                $body        = "fields {$fieldsString}; where themes != (42); limit 500; offset {$offsetValue}; sort {$sortingRule};";
                $pool->as($i)->withHeaders($headers)->withBody($body)->post("https://api.igdb.com/v4/games");
            }
        });

        $flattenReponse = Arr::flatten($responses->json());

//        dd($response->json());

        return $flattenReponse->json();
    }
}
