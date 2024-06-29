<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddAgeRatingsAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_age_ratings';
            $localIdsName       = 'id';
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];

            $recordsIds = collect($records)->pluck('id');

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName);
            });

            $newRecords = collect($records)->filter(function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if ($existingRecordsIds->contains($record['id']) || !array_key_exists('category', $record) || !array_key_exists('rating', $record)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $transformedRecords = $newRecords->map(function ($record) use ($localIdsName) {
                return [
                    $localIdsName          => $record['id'],
                    'category'             => number_format($record['category'], 0, '', ''),
                    'checksum'             => $record['checksum'],
                    'content_descriptions' => json_encode($record['content_descriptions'] ?? []),
                    'rating'               => number_format($record['rating'], 0, '', ''),
                    'rating_cover_url'     => $record['rating_cover_url'] ?? null,
                    'synopsis'             => $record['synopsis'] ?? null,
                    'created_at'           => now(),
                    'updated_at'           => now(),
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
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
