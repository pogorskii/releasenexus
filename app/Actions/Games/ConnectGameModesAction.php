<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConnectGameModesAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords  = 0;
            $existingGameIds = [];
            $existingModeIds = [];

            DB::transaction(function () use (&$existingGameIds, &$existingModeIds) {
                $existingGameIds = DB::table('games')->pluck('origin_id')->toArray();
                $existingModeIds = DB::table('g_modes')->pluck('id')->toArray();
            });

            $pivotRecords = [];

            collect($records)->map(function ($record) use ($existingGameIds, $existingModeIds, &$pivotRecords) {
                if (!in_array($record['id'], $existingGameIds)) {
                    return null;
                }

                foreach ($record['game_modes'] as $mode) {
                    if (!in_array($mode, $existingModeIds)) {
                        continue;
                    }

                    $pivotRecords[] = [
                        'game_id'    => $record['id'],
                        'g_mode_id'  => $mode,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            });

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('game_g_mode')->insert($chunk->toArray());

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
