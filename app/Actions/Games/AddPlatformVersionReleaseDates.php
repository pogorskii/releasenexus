<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddPlatformVersionReleaseDates
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_release_dates';
            $localIdsName       = 'origin_id';
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->where('dateable_type', 'App\Models\GPlatformVersion')->whereIn($localIdsName, $recordsIds)->pluck($localIdsName)->toArray();
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
                    $localIdsName   => $record['id'],
                    'category'      => array_key_exists('category', $record) ? number_format($record['category'], 0, '', '') : null,
                    'checksum'      => $record['checksum'],
                    'created_at'    => now(),
                    'date'          => array_key_exists('date', $record) ? Carbon::createFromTimestamp($record['date'])->toDateTimeString() : null,
                    'human'         => $record['human'] ?? null,
                    'm'             => $record['m'] ?? null,
                    'region'        => array_key_exists('region', $record) ? number_format($record['region'], 0, '', '') : null,
                    'status_id'     => $record['status'] ?? null,
                    'updated_at'    => now(),
                    'y'             => $record['y'] ?? null,
                    'dateable_id'   => $record['platform_version'] ?? null,
                    'dateable_type' => 'App\Models\GPlatformVersion',
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
