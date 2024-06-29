<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddCharacterMugShotsAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_images';
            $localIdsName       = 'origin_id';
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];

            $recordsIds = collect($records)->pluck('image_id');

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, $tableName, $localIdsName) {
                $existingRecordsIds = DB::table($tableName)->whereIn('image_id', $recordsIds)->pluck($localIdsName);
            });

            $newRecords = collect($records)->filter(function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if ($existingRecordsIds->contains($record['image_id'])) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $transformedRecords = $newRecords->map(function ($record) use ($localIdsName) {
                return [
                    $localIdsName   => $record['id'],
                    'collection'    => 'mug_shots',
                    'alpha_channel' => $record['alpha_channel'] ?? false,
                    'animated'      => $record['animated'] ?? false,
                    'checksum'      => $record['checksum'],
                    'height'        => $record['height'] ?? null,
                    'image_id'      => $record['image_id'],
                    'url'           => $record['url'],
                    'width'         => $record['width'] ?? null,
                    'created_at'    => now(),
                    'updated_at'    => now(),
                ];
            });

            $result = DB::table($tableName)->insert($transformedRecords->toArray());
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

            return [
                'written' => $writtenRecords,
                'skipped' => $skippedRecords,
            ];
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
