<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddLanguageSupportsAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_language_supports';
            $localIdsName       = 'id';
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];
            $existingGameIds    = [];

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, &$existingGameIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName)->toArray();
                $existingGameIds    = DB::table('games')->pluck('id')->toArray();
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
                    $localIdsName   => $record['id'],
                    'checksum'      => $record['checksum'],
                    'game_id'       => $record['game'],
                    'g_language_id' => $record['language'],
                    'support_type'  => array_key_exists('language_support_type', $record) ? number_format($record['language_support_type'], 0, '', '') : null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
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
