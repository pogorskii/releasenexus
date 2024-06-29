<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class AddGamesAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'games';
            $localIdsName       = 'id';
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName)->toArray();
            });

            $transformedRecords = collect($records)->map(function ($record) use ($localIdsName, $existingRecordsIds, &$transformedRecords, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return null;
                }

                return [
                    $localIdsName             => $record['id'],
                    'aggregated_rating'       => $record['aggregated_rating'] ?? 0,
                    'aggregated_rating_count' => $record['aggregated_rating_count'] ?? 0,
                    'alternative_names'       => json_encode($record['alternative_names'] ?? []),
                    'category'                => array_key_exists('category', $record) ? number_format($record['category'], 0, '', '') : null,
                    'checksum'                => $record['checksum'],
                    'first_release_date'      => array_key_exists('first_release_date', $record) ? Carbon::createFromTimestamp($record['first_release_date'])->toDateTimeString() : null,
                    'hypes'                   => $record['hypes'] ?? 0,
                    'name'                    => $record['name'],
                    'rating'                  => $record['rating'] ?? 0,
                    'rating_count'            => $record['rating_count'] ?? 0,
                    'slug'                    => $record['slug'],
                    'status'                  => array_key_exists('status', $record) ? number_format($record['status'], 0, '', '') : null,
                    'storyline'               => $record['storyline'] ?? null,
                    'summary'                 => $record['summary'] ?? null,
                    'tags'                    => json_encode($record['tags'] ?? []),
                    'total_rating'            => $record['total_rating'] ?? 0,
                    'total_rating_count'      => $record['total_rating_count'] ?? 0,
                    'url'                     => $record['url'],
                    'version_title'           => $record['version_title'] ?? null,
                    'updated_at'              => now(),
                    'created_at'              => now(),
                ];
            });

            $result = DB::table($tableName)->insert($transformedRecords->toArray());
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

            return [
                'written' => $writtenRecords,
                'skipped' => $skippedRecords,
            ];
        } catch (Throwable $th) {
            dd($th->getMessage());
        }
    }
}
