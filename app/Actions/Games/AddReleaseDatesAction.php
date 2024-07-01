<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddReleaseDatesAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName         = 'g_release_dates';
            $localIdsName      = 'origin_id';
            $writtenRecords    = 0;
            $skippedRecords    = 0;
            $existingRecordIds = collect();
            $existingGameIds   = collect();

            $recordsIds = collect($records)->pluck('id');

            DB::transaction(function () use ($recordsIds, &$existingRecordIds, &$existingGameIds, $tableName, $localIdsName) {
                $existingRecordIds = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName);
                $existingGameIds   = DB::table('games')->pluck('id');
            });

            $newRecords = collect($records)->filter(function ($record) use ($existingRecordIds, &$skippedRecords) {
                if ($existingRecordIds->contains($record['id'])) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $transformedRecords = $newRecords->map(function ($record) use ($localIdsName, $existingGameIds) {
                $safeGameId = array_key_exists('game', $record) && $existingGameIds->contains($record['game']) ? $record['game'] : null;

                return [
                    $localIdsName   => $record['id'],
                    'category'      => array_key_exists('category', $record) ? number_format($record['category'], 0, '', '') : null,
                    'checksum'      => $record['checksum'],
                    'date'          => array_key_exists('date', $record) ? Carbon::createFromTimestamp($record['date'])->toDateTimeString() : null,
                    'human'         => $record['human'] ?? null,
                    'm'             => $record['m'] ?? null,
                    'region'        => array_key_exists('region', $record) ? number_format($record['region'], 0, '', '') : null,
                    'status_id'     => $record['status'] ?? null,
                    'y'             => $record['y'] ?? null,
                    'platform_id'   => $record['platform'] ?? null,
                    'dateable_id'   => $safeGameId,
                    'dateable_type' => $record['game'] ? 'App\Models\Game' : null,
                    'updated_at'    => now(),
                    'created_at'    => now(),
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
