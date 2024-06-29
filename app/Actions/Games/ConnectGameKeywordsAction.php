<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class ConnectGameKeywordsAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingGameIds    = [];
            $existingKeywordIds = [];

            DB::transaction(function () use (&$existingGameIds, &$existingKeywordIds) {
                $existingGameIds    = DB::table('games')->pluck('id')->toArray();
                $existingKeywordIds = DB::table('g_keywords')->pluck('id')->toArray();
            });

            $pivotRecords = [];

            collect($records)->map(function ($record) use ($existingGameIds, $existingKeywordIds, &$pivotRecords) {
                if (!in_array($record['id'], $existingGameIds)) {
                    return null;
                }

                foreach ($record['keywords'] as $keyword) {
                    if (!in_array($keyword, $existingKeywordIds)) {
                        continue;
                    }

                    $pivotRecords[] = [
                        'game_id'      => $record['id'],
                        'g_keyword_id' => $keyword,
                        'created_at'   => now(),
                        'updated_at'   => now(),
                    ];
                }
            });

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('game_g_keyword')->insert($chunk->toArray());

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
