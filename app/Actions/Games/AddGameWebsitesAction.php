<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddGameWebsitesAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_websites';
            $localIdsName       = 'origin_id';
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];

            $recordsIds = collect($records)->pluck('url')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->where('websiteable_type', 'App\Models\Game')->whereIn('url', $recordsIds)->pluck($localIdsName)->toArray();
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
                    $localIdsName      => $record['id'],
                    'category'         => array_key_exists('category', $record) ? number_format($record['category'], 0, '', '') : null,
                    'checksum'         => $record['checksum'],
                    'trusted'          => $record['trusted'] ?? false,
                    'url'              => $record['url'],
                    'websiteable_type' => 'App\Models\Game',
                    'created_at'       => now(),
                    'updated_at'       => now(),
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
