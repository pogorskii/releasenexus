<?php

namespace App\Actions\Games;

use App\Models\Game;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class AddGamesToDB
{
    public static function execute(array $games): array
    {
        try {
            $writtenGames = 0;
            $skippedGames = 0;
            $newGames     = [];

            DB::transaction(function () use ($games, &$newGames, &$skippedGames) {
                foreach ($games as $game) {
                    $gameExists = DB::table('games')->where('origin_id', $game['id'])->exists();

                    if ($gameExists) {
                        $skippedGames++;
                    } else {
                        $newGames[] = $game;
                    }
                }
            });

            $transformedGames = collect($newGames)->map(function ($game) {
                $transformedGame = collect($game)->filter(function ($value, $key) {
                    return match ($key) {
                        'id', 'name', 'slug', 'summary', 'first_release_date', 'aggregated_rating', 'aggregated_rating_count', 'hypes', 'status', 'version_title' => true,
                        default => false,
                    };
                })->map(function ($value, $key) {
                    switch ($key) {
                        case 'id':
                            $key = 'origin_id';
                            break;
                        case 'first_release_date':
                            $value = Carbon::createFromTimestamp($value)->toDateTimeString();
                            break;
                    }

                    return [$key => $value];
                })->collapse()->toArray();

                $transformedGame['synced_at'] = now();

                return $transformedGame;
            });

            DB::transaction(function () use ($transformedGames, &$writtenGames, &$skippedGames) {
                foreach ($transformedGames as $game) {
                    $writtenGame = Game::create($game);

                    if ($writtenGame) {
                        $writtenGames++;
                    }
                }
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
