<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConnectCollectionsFromMembershipsAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords        = 0;
            $existingGameIds       = [];
            $existingCollectionIds = [];

            DB::transaction(function () use (&$existingGameIds, &$existingCollectionIds) {
                $existingGameIds       = DB::table('games')->pluck('origin_id')->toArray();
                $existingCollectionIds = DB::table('g_collections')->pluck('id')->toArray();
            });

            $pivotRecords = collect($records)->map(function ($record) use ($existingGameIds, $existingCollectionIds) {
                if (!in_array($record['game'], $existingGameIds) || !in_array($record['collection'], $existingCollectionIds)) {
                    return null;
                }

                return [
                    'game_id'         => $record['game'],
                    'g_collection_id' => $record['collection'],
                    'main_collection' => false,
                    'type'            => $record['type'] ?? null,
                    'created_at'      => Carbon::now(),
                    'updated_at'      => Carbon::now(),
                ];
            });

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('game_g_collection')->insert($chunk->toArray());
                if ($result) {
                    $writtenRecords += count($chunk);
                }
            });

            return [
                'written' => $writtenRecords,
                'skipped' => $skippedRecords,
            ];
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
