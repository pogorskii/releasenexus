<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddLocalizationsAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_localizations';
            $localIdsName       = 'id';
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];
            $existingGameIds    = [];

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, &$existingGameIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName)->toArray();
                $existingGameIds    = DB::table('games')->pluck('origin_id')->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, $existingGameIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds) || !in_array($record['game'], $existingGameIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$pivotRecords) {
                return [
                    $localIdsName => $record['id'],
                    'checksum'    => $record['checksum'],
                    'game_id'     => $record['game'],
                    'name'        => $record['name'] ?? null,
                    'g_region_id' => $record['region'] ?? null,
                    'created_at'  => Carbon::now(),
                    'updated_at'  => Carbon::now(),
                ];
            })->toArray();

            $result = DB::table($tableName)->insert($transformedRecords);
            if ($result) {
                $writtenRecords += count($transformedRecords);
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
