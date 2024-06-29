<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddVideosAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_videos';
            $localIdsName       = 'id';
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];
            $existingGamesIds   = [];

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, &$existingGamesIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName)->toArray();
                $existingGamesIds   = DB::table('games')->pluck('id')->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $pivotRecords = [];

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$pivotRecords, $existingGamesIds) {
                if (array_key_exists('game', $record) && in_array($record['game'], $existingGamesIds)) {
                    $pivotRecords[] = [
                        'videoable_id'   => $record['game'],
                        'videoable_type' => 'App\Models\Game',
                        'g_video_id'     => $record['id'],
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }

                return [
                    $localIdsName => $record['id'],
                    'checksum'    => $record['checksum'],
                    'name'        => $record['name'] ?? null,
                    'video_id'    => $record['video_id'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            })->toArray();

            $result = DB::table($tableName)->insert($transformedRecords);
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

            if (!empty($pivotRecords)) {
                $result = DB::table('g_videoables')->insert($pivotRecords);
                if ($result) {
                    $writtenRecords += count($pivotRecords);
                }
            }

            return [
                'written' => $writtenRecords,
                'skipped' => $skippedRecords,
            ];
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
