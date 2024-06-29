<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddPlatformsAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_platforms';
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
                    $localIdsName      => $record['id'],
                    'abbreviation'     => $record['abbreviation'] ?? null,
                    'alternative_name' => $record['alternative_name'] ?? null,
                    'category'         => array_key_exists('category', $record) ? number_format($record['category'], 0, '', '') : null,
                    'checksum'         => $record['checksum'],
                    'created_at'       => Carbon::createFromTimestamp($record['created_at'])->toDateTimeString(),
                    'generation'       => $record['generation'] ?? null,
                    'name'             => $record['name'],
                    'platform_family'  => json_encode($record['platform_family'] ?? []),
                    'slug'             => $record['slug'],
                    'summary'          => $record['summary'] ?? null,
                    'updated_at'       => Carbon::createFromTimestamp($record['updated_at'])->toDateTimeString(),
                    'url'              => $record['url'],
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
