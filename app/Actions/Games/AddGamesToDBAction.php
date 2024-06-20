<?php

namespace App\Actions\Games;

use App\Models\Game;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class AddGamesToDBAction
{
    public static function execute(array $games): array
    {
        try {
            $writtenGames = 0;
            $skippedGames = 0;
            $newGames     = [];
            $existingGames = [];

            $gamesIds = collect($games)->pluck('id')->toArray();

            DB::transaction(function () use ($gamesIds, &$newGames, &$existingGames, &$skippedGames) {
                $existingGames[] = DB::table('games')->whereIn('origin_id', $gamesIds)->pluck('origin_id')->toArray();
            });

            foreach ($games as $game) {
                if (!in_array($game['id'], $existingGames)) {
                    $newGames[] = $game;
                } else {
                    $skippedGames++;
                }
            }

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

//            $transformedGames = collect($newGames)->map(function ($game) {
//                $transformedGame = collect($game)->filter(function ($value, $key) {
//                    return match ($key) {
//                        'id', 'aggregated_rating', 'aggregated_rating_count', 'alternative_names', 'category', 'checksum', 'created_at', 'first_release_date', 'hypes', 'name', 'rating', 'rating_count', 'slug', 'status', 'storyline', 'summary', 'tags', 'total_rating', 'total_rating_count', 'updated_at', 'url', 'version_title' => true,
//                        default => false,
//                    };
//                })->map(function ($value, $key) {
//                    switch ($key) {
//                        case 'id':
//                            $key = 'origin_id';
//                            break;
//                        case 'first_release_date':
//                        case 'created_at':
//                        case 'updated_at':
//                            $value = Carbon::createFromTimestamp($value)->toDateTimeString();
//                            break;
//                        case 'category':
//                        case 'status':
//                            $value = number_format($value, 0, '', '');
//                            break;
//                        case 'alternative_names':
//                            $value = json_encode(collect($value)->map(function ($alternativeName) {
//                                return [
//                                    'name'    => $alternativeName['name'],
//                                    'comment' => $alternativeName['comment'] ?? null,
//                                ];
//                            })->toArray());
//                        case 'tags':
//                            $value = json_encode($value);
//                            break;
//                    }
//
//                    return [$key => $value];
//                })->collapse()->toArray();
//
//                $transformedGame['synced_at'] = now();
//
//                return $transformedGame;
//            });

//            DB::transaction(function () use ($transformedGames, &$writtenGames, &$skippedGames) {
//                foreach ($transformedGames as $game) {
//                    $writtenGame = Game::create($game);
//
//                    if ($writtenGame) {
//                        $writtenGames++;
//                    }
//                }
//            });

//dd($transformedGames);
//            DB::table('games')->insert($transformedGames->toArray());

            collect($transformedGames)->chunk(2000)->each(function ($chunk) use (&$writtenGames) {
                Game::create($chunk->toArray());
//                dd($chunk->toArray()[0]);
//                $result = DB::table('games')->insert($chunk->toArray());
//                dd($result);
//                $writtenGames += $chunk->count();
            });

            return [
                'written' => $writtenGames,
                'skipped' => $skippedGames,
            ];
        } catch (Throwable $th) {
            dd($th->getMessage());
        }
    }
}
