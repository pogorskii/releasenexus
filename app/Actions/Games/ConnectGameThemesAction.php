<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ConnectGameThemesAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords   = 0;
            $existingGameIds  = [];
            $existingThemeIds = [];

            DB::transaction(function () use (&$existingGameIds, &$existingThemeIds) {
                $existingGameIds  = DB::table('games')->pluck('origin_id')->toArray();
                $existingThemeIds = DB::table('g_themes')->pluck('id')->toArray();
            });

            $pivotRecords = [];

            collect($records)->map(function ($record) use ($existingGameIds, $existingThemeIds, &$pivotRecords) {
                if (!in_array($record['id'], $existingGameIds)) {
                    return null;
                }

                foreach ($record['themes'] as $theme) {
                    if (!in_array($theme, $existingThemeIds)) {
                        continue;
                    }

                    $pivotRecords[] = [
                        'game_id'    => $record['id'],
                        'g_theme_id' => $theme,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ];
                }
            });

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('game_g_theme')->insert($chunk->toArray());

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
