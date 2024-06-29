<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddFranchisesAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName           = 'g_franchises';
            $localIdsName        = 'id';
            $pivotTableName      = 'game_g_franchise';
            $writtenRecords      = 0;
            $skippedRecords      = 0;
            $existingRecordsIds  = [];
            $allExistingGamesIds = DB::table('games')->pluck('id')->toArray();

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName)->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $pivotRecords = [];

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$pivotRecords, $allExistingGamesIds) {
                if (array_key_exists('games', $record) && !empty($record['games'])) {
                    foreach ($record['games'] as $game) {
                        $gameExists = in_array($game, $allExistingGamesIds);
                        if (!$gameExists) {
                            continue;
                        }

                        $pivotRecords[] = [
                            'g_franchise_id' => $record['id'],
                            'game_id'        => $game,
                            'main_franchise' => false,
                            'created_at'     => now(),
                            'updated_at'     => now(),
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
            })->toArray();

            $result = DB::table($tableName)->insert($transformedRecords);
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use ($pivotTableName, &$writtenRecords, &$skippedRecords) {
                $result = DB::table($pivotTableName)->insert($chunk->toArray());
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
