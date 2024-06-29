<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class ConnectChildGamesAction
{
    public static function makeParentConnectionRecord(int $gameId, int $parentId): array
    {
        return [
            'id'             => $gameId,
            'parent_game_id' => $parentId,
            'updated_at'     => now(),
            'created_at'     => now(),
        ];
    }

    public static function execute(array $records): array
    {
        try {
            $tableName        = 'games';
            $localIdsName     = 'id';
            $writtenRecords   = 0;
            $skippedRecords   = 0;
            $existingGamesIds = [];

            DB::transaction(function () use (&$existingGamesIds, $tableName, $localIdsName) {
                $existingGamesIds = DB::table($tableName)->pluck('id')->toArray();
            });

            $transformedRecords = [];

            collect($records)->map(function ($record) use ($localIdsName, $existingGamesIds, &$skippedRecords, &$transformedRecords) {
                $safeParentGameId    = array_key_exists('parent_game', $record) && in_array($record['parent_game'], $existingGamesIds) ? $record['parent_game'] : null;
                $safeVersionParentId = array_key_exists('version_parent', $record) && in_array($record['version_parent'], $existingGamesIds) ? $record['version_parent'] : null;

                if ($safeParentGameId !== null || $safeVersionParentId !== null) {
                    $transformedRecords[] = [
                        $localIdsName       => $record['id'],
                        'parent_game_id'    => $safeParentGameId,
                        'version_parent_id' => $safeVersionParentId,
                        'updated_at'        => now(),
                        'created_at'        => now(),
                    ];
                } else {
                    if (array_key_exists('bundles', $record)) {
                        $safeChildIds = collect($record['bundles'])->filter(function ($bundle) use ($existingGamesIds) {
                            return in_array($bundle, $existingGamesIds);
                        });

                        $safeChildIds->each(function ($safeChildId) use ($record, &$transformedRecords) {
                            $transformedRecords[] = self::makeParentConnectionRecord($safeChildId, $record['id']);
                        });
                    }

                    if (array_key_exists('dlcs', $record)) {
                        $safeChildIds = collect($record['dlcs'])->filter(function ($dlc) use ($existingGamesIds) {
                            return in_array($dlc, $existingGamesIds);
                        });

                        $safeChildIds->each(function ($safeChildId) use ($record, &$transformedRecords) {
                            $transformedRecords[] = self::makeParentConnectionRecord($safeChildId, $record['id']);
                        });
                    }

                    if (array_key_exists('expanded_games', $record)) {
                        $safeChildIds = collect($record['expanded_games'])->filter(function ($expansion) use ($existingGamesIds) {
                            return in_array($expansion, $existingGamesIds);
                        });

                        $safeChildIds->each(function ($safeChildId) use ($record, &$transformedRecords) {
                            $transformedRecords[] = self::makeParentConnectionRecord($safeChildId, $record['id']);
                        });
                    }

                    if (array_key_exists('expansions', $record)) {
                        $safeChildIds = collect($record['expansions'])->filter(function ($standaloneExpansion) use ($existingGamesIds) {
                            return in_array($standaloneExpansion, $existingGamesIds);
                        });

                        $safeChildIds->each(function ($safeChildId) use ($record, &$transformedRecords) {
                            $transformedRecords[] = self::makeParentConnectionRecord($safeChildId, $record['id']);
                        });
                    }

                    if (array_key_exists('forks', $record)) {
                        $safeChildIds = collect($record['forks'])->filter(function ($standaloneExpansion) use ($existingGamesIds) {
                            return in_array($standaloneExpansion, $existingGamesIds);
                        });

                        $safeChildIds->each(function ($safeChildId) use ($record, &$transformedRecords) {
                            $transformedRecords[] = self::makeParentConnectionRecord($safeChildId, $record['id']);
                        });
                    }

                    if (array_key_exists('ports', $record)) {
                        $safeChildIds = collect($record['ports'])->filter(function ($standaloneExpansion) use ($existingGamesIds) {
                            return in_array($standaloneExpansion, $existingGamesIds);
                        });

                        $safeChildIds->each(function ($safeChildId) use ($record, &$transformedRecords) {
                            $transformedRecords[] = self::makeParentConnectionRecord($safeChildId, $record['id']);
                        });
                    }

                    if (array_key_exists('remakes', $record)) {
                        $safeChildIds = collect($record['remakes'])->filter(function ($standaloneExpansion) use ($existingGamesIds) {
                            return in_array($standaloneExpansion, $existingGamesIds);
                        });

                        $safeChildIds->each(function ($safeChildId) use ($record, &$transformedRecords) {
                            $transformedRecords[] = self::makeParentConnectionRecord($safeChildId, $record['id']);
                        });
                    }

                    if (array_key_exists('remasters', $record)) {
                        $safeChildIds = collect($record['remasters'])->filter(function ($standaloneExpansion) use ($existingGamesIds) {
                            return in_array($standaloneExpansion, $existingGamesIds);
                        });

                        $safeChildIds->each(function ($safeChildId) use ($record, &$transformedRecords) {
                            $transformedRecords[] = self::makeParentConnectionRecord($safeChildId, $record['id']);
                        });
                    }

                    if (array_key_exists('standalone_expansions', $record)) {
                        $safeChildIds = collect($record['standalone_expansions'])->filter(function ($standaloneExpansion) use ($existingGamesIds) {
                            return in_array($standaloneExpansion, $existingGamesIds);
                        });

                        $safeChildIds->each(function ($safeChildId) use ($record, &$transformedRecords) {
                            $transformedRecords[] = self::makeParentConnectionRecord($safeChildId, $record['id']);
                        });
                    }
                }
            });

            collect($transformedRecords)->chunk(1000)->each(function ($chunk) use ($tableName, &$writtenRecords) {
                DB::transaction(function () use ($tableName, $chunk, &$writtenRecords) {
                    foreach ($chunk as $record) {
                        DB::table($tableName)->where('id', $record['id'])->update($record);
                        $writtenRecords++;
                    }
                });
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
