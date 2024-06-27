<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddReleaseDateStatuses
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_release_date_statuses';
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

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName) {
                return [
                    $localIdsName => $record['id'],
                    'checksum'    => $record['checksum'],
                    'created_at'  => Carbon::createFromTimestamp($record['created_at'])->toDateTimeString(),
                    'description' => $record['description'] ?? null,
                    'name'        => $record['name'],
                    'updated_at'  => Carbon::createFromTimestamp($record['updated_at'])->toDateTimeString(),
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
