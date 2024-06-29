<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConnectCollectionsFromGamesAction
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

            $pivotRecords = [];

            collect($records)->map(function ($record) use ($existingGameIds, $existingCollectionIds, &$pivotRecords) {
                if (!in_array($record['id'], $existingGameIds)) {
                    return null;
                }

                if (array_key_exists('collection', $record)) {
                    if (!in_array($record['collection'], $existingCollectionIds)) {
                        return null;
                    }
                    $pivotRecords[] = [
                        'game_id'         => $record['id'],
                        'g_collection_id' => $record['collection'],
                        'main_collection' => true,
                        'created_at'      => Carbon::now(),
                        'updated_at'      => Carbon::now(),
                    ];
                }

                if (array_key_exists('collections', $record) & !empty($record['collections'])) {
                    foreach ($record['collections'] as $collection) {
                        if (!in_array($collection, $existingCollectionIds)) {
                            continue;
                        }

                        $pivotRecords[] = [
                            'game_id'         => $record['id'],
                            'g_collection_id' => $collection,
                            'main_collection' => false,
                            'created_at'      => Carbon::now(),
                            'updated_at'      => Carbon::now(),
                        ];
                    }
                }
            });

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('game_g_collection')->upsert($chunk->toArray(), [
                    'game_id',
                    'g_collection_id',
                ], ['main_collection', 'updated_at']);
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
