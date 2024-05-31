<?php

namespace App\Actions\IGDB;

use Illuminate\Support\Facades\Http;

class FetchGames
{
    public static function execute(): array
    {
        $response = Http::withHeaders([
            'Accept'        => 'application/json',
            'Client-ID'     => config('services.igdb.client_id'),
            'Authorization' => 'Bearer '.config('services.igdb.access_token'),
        ])->withBody('fields *, age_ratings.*, age_ratings.content_descriptions.*, alternative_names.*, cover.*, game_localizations.*, external_games.*, language_supports.*, release_dates.*, screenshots.*, videos.*, websites.*, collection.*, collections.*, franchise.*, franchises.*, game_engines.*;	where themes != (42); limit 500; sort updated_at desc;')->post('https://api.igdb.com/v4/games');

        return $response->json();
    }
}
