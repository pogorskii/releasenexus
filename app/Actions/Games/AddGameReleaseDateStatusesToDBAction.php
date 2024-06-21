<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddGameReleaseDateStatusesToDBAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds) {
                $existingRecordsIds = DB::table('g_release_date_statuses')->whereIn('origin_id', $recordsIds)->pluck('origin_id')->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $transformedRecords = collect($newRecords)->map(function ($record) {
                return [
                    'id'          => $record['id'],
                    'checksum'    => $record['checksum'],
                    'created_at'  => Carbon::createFromTimestamp($record['created_at'])->toDateTimeString(),
                    'description' => $record['description'] ?? null,
                    'name'        => $record['name'],
                    'updated_at'  => Carbon::createFromTimestamp($record['updated_at'])->toDateTimeString(),
                ];
            })->toArray();

            $result = DB::table('g_release_date_statuses')->insert($transformedRecords);
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
