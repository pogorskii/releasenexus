<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddExternalGamesAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName            = 'g_external_games';
            $localIdsName         = 'id';
            $writtenRecords       = 0;
            $skippedRecords       = 0;
            $existingRecordsIds   = [];
            $existingPlatformsIds = [];
            $existingGamesIds     = [];

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, &$existingPlatformsIds, &$existingGamesIds, $tableName, $localIdsName) {
                $existingRecordsIds   = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName)->toArray();
                $existingPlatformsIds = DB::table('g_platforms')->pluck('id')->toArray();
                $existingGamesIds     = DB::table('games')->pluck('origin_id')->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $pivotRecords = [];

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$pivotRecords, $existingPlatformsIds, $existingGamesIds) {
                if (array_key_exists('platform', $record) && in_array($record['platform'], $existingPlatformsIds)) {
                    $pivotRecords[] = [
                        'g_external_game_id' => $record['id'],
                        'g_platform_id'      => $record['platform'],
                        'created_at'         => Carbon::now(),
                        'updated_at'         => Carbon::now(),
                    ];
                }

                return [
                    $localIdsName => $record['id'],
                    'category'    => array_key_exists('category', $record) ? number_format($record['category'], 0, '', '') : null,
                    'checksum'    => $record['checksum'],
                    'countries'   => json_encode($record['countries'] ?? []),
                    'game_id'     => array_key_exists('game', $record) && in_array($record['game'], $existingGamesIds) ? $record['game'] : null,
                    'media'       => array_key_exists('media', $record) ? number_format($record['media'], 0, '', '') : null,
                    'name'        => $record['name'] ?? null,
                    'uid'         => $record['uid'] ?? null,
                    'url'         => $record['url'] ?? null,
                    'year'        => $record['year'] ?? null,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ];
            })->toArray();

            $result = DB::table($tableName)->insert($transformedRecords);
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

            if (!empty($pivotRecords)) {
                $pivotTableName = 'g_external_game_g_platform';
                $pivotResult    = DB::table($pivotTableName)->insert($pivotRecords);
                if ($pivotResult) {
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
