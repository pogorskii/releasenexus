<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddCharactersAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName           = 'g_characters';
            $localIdsName        = 'id';
            $writtenRecords      = 0;
            $skippedRecords      = 0;
            $existingRecordsIds  = [];
            $allExistingGamesIds = DB::table('games')->pluck('id');

            $recordsIds = collect($records)->pluck('id');

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName);
            });

            $newRecords = collect($records)->filter(function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if ($existingRecordsIds->contains($record['id'])) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $pivotRecords = [];

            $transformedRecords = $newRecords->map(function ($record) use ($localIdsName, &$pivotRecords, $allExistingGamesIds) {
                if (array_key_exists('games', $record) && !empty($record['games'])) {
                    foreach ($record['games'] as $game) {
                        $gameExists = $allExistingGamesIds->contains($game);
                        if (!$gameExists) {
                            continue;
                        }

                        $pivotRecords[] = [
                            'g_character_id' => $record['id'],
                            'game_id'        => $game,
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ];
                    }
                }

                return [
                    $localIdsName  => $record['id'],
                    'akas'         => json_encode($record['akas'] ?? []),
                    'checksum'     => $record['checksum'],
                    'country_name' => $record['country_name'] ?? null,
                    'description'  => $record['description'] ?? null,
                    'gender'       => array_key_exists('gender', $record) ? number_format($record['gender'], 0, '', '') : null,
                    'name'         => $record['name'],
                    'slug'         => $record['slug'],
                    'species'      => array_key_exists('species', $record) ? number_format($record['species'], 0, '', '') : null,
                    'url'          => $record['url'],
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            });

            $result = DB::table($tableName)->insert($transformedRecords->toArray());
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('game_g_character')->insert($chunk->toArray());
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
