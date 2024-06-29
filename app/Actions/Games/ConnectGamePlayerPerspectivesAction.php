<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConnectGamePlayerPerspectivesAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords      = 0;
            $allExistingGamesIds = DB::table('games')->pluck('origin_id')->toArray();
            $pivotRecords        = [];

            collect($records)->map(function ($record) use (&$pivotRecords, $allExistingGamesIds) {
                if (!in_array($record['id'], $allExistingGamesIds)) {
                    return;
                }

                foreach ($record['player_perspectives'] as $perspective) {
                    $pivotRecords[] = [
                        'game_id'                 => $record['id'],
                        'g_player_perspective_id' => $perspective,
                        'created_at'              => Carbon::now(),
                        'updated_at'              => Carbon::now(),
                    ];
                }
            });

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('game_g_player_perspective')->insert($chunk->toArray());
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
