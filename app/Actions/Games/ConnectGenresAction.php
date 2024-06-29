<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class ConnectGenresAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords   = 0;
            $existingGameIds  = [];
            $existingGenreIds = [];

            DB::transaction(function () use (&$existingGameIds, &$existingGenreIds) {
                $existingGameIds  = DB::table('games')->pluck('id')->toArray();
                $existingGenreIds = DB::table('g_genres')->pluck('id')->toArray();
            });

            $pivotRecords = [];

            collect($records)->map(function ($record) use ($existingGameIds, $existingGenreIds, &$pivotRecords) {
                if (!in_array($record['id'], $existingGameIds)) {
                    return null;
                }

                foreach ($record['genres'] as $genre) {
                    if (!in_array($genre, $existingGenreIds)) {
                        continue;
                    }

                    $pivotRecords[] = [
                        'game_id'    => $record['id'],
                        'g_genre_id' => $genre,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            });

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('game_g_genre')->insert($chunk->toArray());

                if ($result) {
                    $writtenRecords += count($chunk);
                }
            });

            return [
                'written' => $writtenRecords,
                'skipped' => $skippedRecords,
            ];
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
