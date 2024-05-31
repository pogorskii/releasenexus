<?php

namespace App\Actions\IGDB;

use App\Models\Game;
use Illuminate\Support\Facades\DB;

class SaveGamesToDB
{
    public static function execute(array $games)
    {
        DB::table('games')->transaction(function () use ($games) {
            foreach ($games as $game) {
                $game = Game::create([
                    'igdb_id' => $game['id'],
                    'name' => $game['name'],
                    'slug' => $game['slug'],
                    'summary' => $game['summary'],
                    'cover' => $game['cover']['url'],
                    'platforms' => $game['platforms'],
                    'genres' => $game['genres'],
                    'release_date' => $game['release_dates'][0]['human'],
                ]);
//                $game = [
//                    'igdb_id' => $game['id'],
//                    'name' => $game['name'],
//                    'slug' => $game['slug'],
//                    'summary' => $game['summary'],
//                    'cover' => $game['cover']['url'],
//                    'platforms' => $game['platforms'],
//                    'genres' => $game['genres'],
//                    'release_date' => $game['release_dates'][0]['human'],
//                ];

                // Save $game to the database
            }
        });

            // Save $game to the database
        }
    }
}
