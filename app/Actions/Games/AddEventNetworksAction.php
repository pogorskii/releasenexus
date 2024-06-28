<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddEventNetworksAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName               = 'g_event_networks';
            $localIdsName            = 'id';
            $writtenRecords          = 0;
            $skippedRecords          = 0;
            $existingRecordsIds      = [];
            $existingEventsIds       = [];
            $existingNetworkTypesIds = [];

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, &$existingEventsIds, &$existingNetworkTypesIds, $tableName, $localIdsName) {
                $existingRecordsIds      = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName)->toArray();
                $existingEventsIds       = DB::table('g_events')->pluck('id')->toArray();
                $existingNetworkTypesIds = DB::table('g_network_types')->pluck('id')->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, $existingEventsIds, $existingNetworkTypesIds) {
                $safeEventId       = array_key_exists('event', $record) && in_array($record['event'], $existingEventsIds) ? $record['event'] : null;
                $safeNetworkTypeId = array_key_exists('network_type', $record) && in_array($record['network_type'], $existingNetworkTypesIds) ? $record['network_type'] : null;

                return [
                    $localIdsName       => $record['id'],
                    'checksum'          => $record['checksum'],
                    'g_event_id'        => $safeEventId,
                    'g_network_type_id' => $safeNetworkTypeId,
                    'url'               => $record['url'],
                    'created_at'        => now(),
                    'updated_at'        => now(),
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
