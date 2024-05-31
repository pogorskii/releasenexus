<?php

namespace App\Http\Controllers;

use App\Actions\IGDB\RefreshIGDBAccessTokenAction;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class TestController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = (new UserResource($request->user()))->resolve();

            $token = 'sdsadsad';

            $response = Http::withHeaders([
                'Accept'        => 'application/json',
                'Client-ID'     => config('services.igdb.client_id'),
                'Authorization' => 'Bearer '.config('services.igdb.access_token'),
            ])->withBody('fields *, age_ratings.*, age_ratings.content_descriptions.*, alternative_names.*, cover.*, game_localizations.*, external_games.*, language_supports.*, release_dates.*, screenshots.*, videos.*, websites.*, collection.*, collections.*, franchise.*, franchises.*, game_engines.*;	where themes != (42); limit 500; sort updated_at desc;')->post('https://api.igdb.com/v4/games');

            return Inertia::render('Dashboard', [
                'user'  => $user,
                'games' => $response->json(),
            ]);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
