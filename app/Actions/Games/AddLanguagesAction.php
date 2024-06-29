<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddLanguagesAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_languages';
            $localIdsName       = 'id';
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName)->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$pivotRecords) {
                return [
                    $localIdsName => $record['id'],
                    'checksum'    => $record['checksum'],
                    'name'        => $record['name'],
                    'native_name' => $record['native_name'],
                    'locale'      => $record['locale'] ?? null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
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
