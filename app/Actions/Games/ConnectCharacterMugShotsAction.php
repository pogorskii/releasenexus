<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class ConnectCharacterMugShotsAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords        = 0;
            $skippedRecords        = 0;
            $existingCharactersIds = [];
            $existingMugShots      = [];

            DB::transaction(function () use (&$existingCharactersIds, &$existingMugShots) {
                $existingCharactersIds = DB::table('g_characters')->pluck('id')->toArray();
                $existingMugShots      = DB::table('g_images')->where('collection', 'mug_shots')->get();
            });

            $existingMugShotsIds = $existingMugShots->pluck('origin_id')->toArray();

            $pivotRecords = collect($records)->map(function ($record) use ($existingCharactersIds, $existingMugShots, $existingMugShotsIds) {
                if (!in_array($record['id'], $existingCharactersIds) || !in_array($record['mug_shot'], $existingMugShotsIds)) {
                    return null;
                }

                return [
                    'imageable_id'   => $record['id'],
                    'imageable_type' => 'App\Models\GCharacter',
                    'g_image_id'     => $existingMugShots->firstWhere('origin_id', $record['mug_shot'])->image_id,
                    'collection'     => 'mug_shots',
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ];
            });

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('g_imageables')->insert($chunk->toArray());
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
