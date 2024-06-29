<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddMultiplayerModesAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName           = 'g_multiplayer_modes';
            $localIdsName        = 'id';
            $writtenRecords      = 0;
            $skippedRecords      = 0;
            $existingRecordsIds  = [];
            $existingGameIds     = [];
            $existingPlatformIds = [];

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, &$existingGameIds, &$existingPlatformIds, $tableName, $localIdsName) {
                $existingRecordsIds  = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName)->toArray();
                $existingGameIds     = DB::table('games')->pluck('origin_id')->toArray();
                $existingPlatformIds = DB::table('g_platforms')->pluck('id')->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, $existingGameIds, $existingPlatformIds) {
                $safeGameId     = array_key_exists('game', $record) && in_array($record['game'], $existingGameIds) ? $record['game'] : null;
                $safePlatformId = array_key_exists('platform', $record) && in_array($record['platform'], $existingPlatformIds) ? $record['platform'] : null;

                return [
                    $localIdsName        => $record['id'],
                    'campaign_coop'      => $record['campaigncoop'],
                    'checksum'           => $record['checksum'],
                    'drop_in'            => $record['dropin'] ?? null,
                    'game_id'            => $safeGameId,
                    'lan_coop'           => $record['lancoop'] ?? null,
                    'offline_coop'       => $record['offlinecoop'] ?? null,
                    'offline_coop_max'   => $record['offlinecoopmax'] ?? null,
                    'online_coop'        => $record['onlinecoop'] ?? null,
                    'online_coop_max'    => $record['onlinecoopmax'] ?? null,
                    'offline_max'        => $record['offlinemax'] ?? null,
                    'online_max'         => $record['onlinemax'] ?? null,
                    'g_platform_id'      => $safePlatformId,
                    'splitscreen'        => $record['splitscreen'] ?? null,
                    'splitscreen_online' => $record['splitscreenonline'] ?? null,
                    'created_at'         => Carbon::now(),
                    'updated_at'         => Carbon::now(),
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
