<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConnectAgeRatingsAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords       = 0;
            $existingGameIds      = [];
            $existingAgeRatingIds = [];

            DB::transaction(function () use (&$existingGameIds, &$existingAgeRatingIds) {
                $existingGameIds      = DB::table('games')->pluck('origin_id')->toArray();
                $existingAgeRatingIds = DB::table('g_age_ratings')->pluck('id')->toArray();
            });

            $pivotRecords = [];

            collect($records)->map(function ($record) use ($existingGameIds, $existingAgeRatingIds, &$pivotRecords) {
                if (!in_array($record['id'], $existingGameIds)) {
                    return null;
                }

                foreach ($record['age_ratings'] as $ageRating) {
                    if (!in_array($ageRating, $existingAgeRatingIds)) {
                        continue;
                    }

                    $pivotRecords[] = [
                        'game_id'         => $record['id'],
                        'g_age_rating_id' => $ageRating,
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                    ];
                }
            });

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('game_g_age_rating')->insert($chunk->toArray());

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
