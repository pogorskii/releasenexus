<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddEventLogosAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_images';
            $localIdsName       = 'origin_id';
            $morphTableName     = 'g_imageables';
            $morphLocalIdsName  = 'g_image_id';
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];
            $existingEventsIds  = [];

            $recordsIds = collect($records)->pluck('image_id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, &$existingEventsIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->whereIn('image_id', $recordsIds)->pluck($localIdsName)->toArray();
                $existingEventsIds  = DB::table('g_events')->pluck('id')->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['image_id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $morphRecords = [];

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$morphRecords, $morphLocalIdsName, $existingEventsIds) {
                $safeEventId = array_key_exists('event', $record) && in_array($record['event'], $existingEventsIds) ? $record['event'] : null;

                $morphRecords[] = [
                    $morphLocalIdsName => $record['image_id'],
                    'imageable_id'     => $safeEventId,
                    'imageable_type'   => 'App\Models\GEvent',
                    'collection'       => 'event_logos',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ];

                return [
                    $localIdsName   => $record['id'],
                    'collection'    => 'event_logos',
                    'alpha_channel' => $record['alpha_channel'] ?? false,
                    'animated'      => $record['animated'] ?? false,
                    'checksum'      => $record['checksum'],
                    'height'        => $record['height'] ?? null,
                    'image_id'      => $record['image_id'],
                    'url'           => $record['url'],
                    'width'         => $record['width'] ?? null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            })->toArray();

            $result         = DB::table($tableName)->upsert($transformedRecords, ['image_id'], [
                'created_at',
                'updated_at',
            ]);
            $writtenRecords += $result;

            $result         = DB::table($morphTableName)->upsert($morphRecords, [
                'imageable_id',
                'imageable_type',
                $morphLocalIdsName,
            ], ['imageable_id', 'created_at', 'updated_at']);
            $writtenRecords += $result;

            return [
                'written' => $writtenRecords,
                'skipped' => $skippedRecords,
            ];
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
