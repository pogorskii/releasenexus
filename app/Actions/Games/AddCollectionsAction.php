<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddCollectionsAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_collections';
            $localIdsName       = 'id';
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];
            $existingGamesIds   = DB::table('games')->pluck('id');

            $recordsIds = collect($records)->pluck('id');

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName);
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if ($existingRecordsIds->contains($record['id'])) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $pivotRecords = [];

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$pivotRecords, $existingGamesIds) {
                if (array_key_exists('games', $record) && !empty($record['games'])) {
                    foreach ($record['games'] as $game) {
                        if (!$existingGamesIds->contains($game)) {
                            continue;
                        }

                        $pivotRecords[] = [
                            'g_collection_id' => $record['id'],
                            'game_id'         => $game,
                            'main_collection' => false,
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }
                }

                return [
                    $localIdsName => $record['id'],
                    'checksum'    => $record['checksum'],
                    'name'        => $record['name'],
                    'slug'        => $record['slug'],
                    'url'         => $record['url'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            });

            $result = DB::table($tableName)->insert($transformedRecords->toArray());
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

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
