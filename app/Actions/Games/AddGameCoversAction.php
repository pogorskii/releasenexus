<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddGameCoversAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName          = 'g_images';
            $localIdsName       = 'origin_id';
            $morphTableName     = 'g_imageables';
            $morphLocalIdsName  = 'g_image_id';
            $writtenRecords     = 0;
            $skippedRecords     = 0;
            $existingRecordsIds = [];

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

            $morphRecords = [];

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$morphRecords, $morphLocalIdsName) {
                $morphRecords[] = [
                    $morphLocalIdsName => $record['image_id'],
                    'imageable_id'     => $record['game'] ?? null,
                    'imageable_type'   => 'App\Models\Game',
                    'collection'       => 'covers',
                    'created_at'       => Carbon::now(),
                    'updated_at'       => Carbon::now(),
                ];

                return [
                    $localIdsName   => $record['id'],
                    'alpha_channel' => $record['alpha_channel'] ?? false,
                    'animated'      => $record['animated'] ?? false,
                    'checksum'      => $record['checksum'],
                    'height'        => $record['height'] ?? null,
                    'image_id'      => $record['image_id'],
                    'url'           => $record['url'],
                    'width'         => $record['width'] ?? null,
                    'created_at'    => Carbon::now(),
                    'updated_at'    => Carbon::now(),
                ];
            })->toArray();

            $result = DB::table($tableName)->insert($transformedRecords);
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

            $result = DB::table($morphTableName)->insert($morphRecords);
            if ($result) {
                $writtenRecords += count($morphRecords);
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
