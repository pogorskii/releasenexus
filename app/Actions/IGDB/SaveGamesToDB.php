<?php

namespace App\Actions\IGDB;

use App\Models\Game;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaveGamesToDB
{
    public static function execute(array $games): void
    {
//        dd($games);
try {
//    dd($games[0]);
    $game = $games[0];
    $wrtittenGame = Game::create([
        'origin_id' => $game['id'],
        'name' => $game['name'],
        'slug' => $game['slug'],
        'summary' => $game['summary'],
        'first_release_date' => array_key_exists('first_release_date', $game) ? Carbon::createFromTimestamp($game['first_release_date'])->toDateTimeString() : null,
        'aggregated_rating' => array_key_exists('aggregated_rating', $game) ? $game['aggregated_rating'] : null,
        'aggregated_rating_count' => array_key_exists('aggregated_rating_count', $game) ? $game['aggregated_rating_count'] : null,
        'hypes' => $game['hypes'],
//        Check if "status" key exists in the array
        'status' => array_key_exists('status', $game) ? $game['status'] : null,
'version_title' => array_key_exists('version_title', $game) ? $game['version_title'] : null,
//        'version_title' => $game['version_title'],
        'synced_at' => now(),
    ]);

//    dd($wrtittenGame);
} catch (\Throwable $th) {
    dd($th->getMessage());
}


//        $writtenGames = 0;
//        $skippedGames = 0;
//
//        foreach ($games as $game) {
//            $gameExists = DB::table('games')->where('origin_id', $game['id'])->exists();
//
//            if ($gameExists) {
//                $skippedGames++;
//                continue;
//            }
//
//            DB::table('games')->insert([
//                'origin_id' => $game['id'],
//                'name' => $game['name'],
//                'slug' => $game['slug'],
//                'summary' => $game['summary'],
//                'first_release_date' => $game['first_release_date'],
//                'aggregated_rating' => $game['aggregated_rating'],
//                'aggregated_rating_count' => $game['aggregated_rating_count'],
//                'follows' => $game['follows'],
//                'hypes' => $game['hypes'],
//                'status' => $game['status'],
//                'version_title' => $game['version_title'],
//                'synced_at' => now(),
//                'created_at' => now(),
//                'updated_at' => now(),
//            ]);
//
//            $writtenGames++;
//        }
//
//        // Log the number of written and skipped games
//        Log::info("Written games: {$writtenGames}");
//        Log::info("Skipped games: {$skippedGames}");

//        DB::transaction(function () use ($games) {
//            foreach ($games as $game) {
//                DB::table('games')->insert([
//                    'origin_id' => $game['id'],
//                    'name' => $game['name'],
//                    'slug' => $game['slug'],
//                    'summary' => $game['summary'],
//                    'first_release_date' => $game['first_release_date'],
//                    'aggregated_rating' => $game['aggregated_rating'],
//                    'aggregated_rating_count' => $game['aggregated_rating_count'],
//                    'follows' => $game['follows'],
//                    'hypes' => $game['hypes'],
//                    'status' => $game['status'],
//                    'version_title' => $game['version_title'],
//                    'synced_at' => now(),
//                    'created_at' => now(),
//                    'updated_at' => now(),
//                ]);
//            }
//        });
    }
}
