<?php

namespace App\Actions\IGDB;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class RefreshIGDBAccessTokenAction
{
    public static function execute(): string
    {
        $clientId     = Config::get('services.igdb.client_id');
        $clientSecret = Config::get('services.igdb.client_secret');

        $response = Http::post('https://id.twitch.tv/oauth2/token', [
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'grant_type'    => 'client_credentials',
        ]);

        $token = $response['access_token'];

        Config::set('services.igdb.access_token', $token);

        return $token;
    }
}
