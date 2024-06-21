<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class AddGamesToDBAction
{
    public static function execute(array $games): array
    {
        try {
            $writtenGames     = 0;
            $skippedGames     = 0;
            $existingGamesIds = [];

            $gamesIds = collect($games)->pluck('id')->toArray();

            DB::transaction(function () use ($gamesIds, &$existingGamesIds) {
                $existingGamesIds = DB::table('games')->whereIn('origin_id', $gamesIds)->pluck('origin_id')->toArray();
            });

            $newGames = array_filter($games, function ($game) use ($existingGamesIds, &$skippedGames) {
                if (in_array($game['id'], $existingGamesIds)) {
                    $skippedGames++;

                    return false;
                }

                return true;
            });

            $transformedGames = collect($newGames)->map(function ($game) {
                return [
                    'origin_id'               => $game['id'],
                    'aggregated_rating'       => $game['aggregated_rating'] ?? 0,
                    'aggregated_rating_count' => $game['aggregated_rating_count'] ?? 0,
                    'alternative_names'       => json_encode($game['alternative_names'] ?? []),
                    'category'                => array_key_exists('category', $game) ? number_format($game['category'], 0, '', '') : null,
                    'checksum'                => $game['checksum'],
                    'created_at'              => Carbon::createFromTimestamp($game['created_at'])->toDateTimeString(),
                    'first_release_date'      => array_key_exists('first_release_date', $game) ? Carbon::createFromTimestamp($game['first_release_date'])->toDateTimeString() : null,
                    'hypes'                   => $game['hypes'] ?? 0,
                    'name'                    => $game['name'],
                    'rating'                  => $game['rating'] ?? 0,
                    'rating_count'            => $game['rating_count'] ?? 0,
                    'slug'                    => $game['slug'],
                    'status'                  => array_key_exists('status', $game) ? number_format($game['status'], 0, '', '') : null,
                    'storyline'               => $game['storyline'] ?? null,
                    'summary'                 => $game['summary'] ?? null,
                    'tags'                    => json_encode($game['tags'] ?? []),
                    'total_rating'            => $game['total_rating'] ?? 0,
                    'total_rating_count'      => $game['total_rating_count'] ?? 0,
                    'updated_at'              => Carbon::createFromTimestamp($game['updated_at'])->toDateTimeString(),
                    'url'                     => $game['url'],
                    'version_title'           => $game['version_title'] ?? null,
                    'synced_at'               => now(),
                ];
            })->toArray();

            $result = DB::table('games')->insert($transformedGames);
            if ($result) {
                $writtenGames += count($transformedGames);
            }

            return [
                'written' => $writtenGames,
                'skipped' => $skippedGames,
            ];
        } catch (Throwable $th) {
            dd($th->getMessage());
        }
    }
}
