<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConnectFranchisesAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords       = 0;
            $existingGameIds      = [];
            $existingFranchiseIds = [];

            DB::transaction(function () use (&$existingGameIds, &$existingFranchiseIds) {
                $existingGameIds      = DB::table('games')->pluck('origin_id')->toArray();
                $existingFranchiseIds = DB::table('g_franchises')->pluck('id')->toArray();
            });

            $pivotRecords = [];

            collect($records)->map(function ($record) use (&$pivotRecords, $existingGameIds, $existingFranchiseIds) {
                if (!in_array($record['id'], $existingGameIds)) {
                    return null;
                }

                if (array_key_exists('franchise', $record) && in_array($record['franchise'], $existingFranchiseIds)) {
                    $pivotRecords[] = [
                        'game_id'        => $record['id'],
                        'g_franchise_id' => $record['franchise'],
                        'main_franchise' => true,
                        'created_at'     => Carbon::now(),
                        'updated_at'     => Carbon::now(),
                    ];
                }

                if (array_key_exists('franchises', $record)) {
                    foreach ($record['franchises'] as $franchise) {
                        if (!in_array($franchise, $existingFranchiseIds)) {
                            continue;
                        }

                        $pivotRecords[] = [
                            'game_id'        => $record['id'],
                            'g_franchise_id' => $franchise,
                            'main_franchise' => false,
                            'created_at'     => Carbon::now(),
                            'updated_at'     => Carbon::now(),
                        ];
                    }
                }
            });

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('game_g_franchise')->upsert($chunk->toArray(), [
                    'game_id',
                    'g_franchise_id',
                ], ['main_franchise']);
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
