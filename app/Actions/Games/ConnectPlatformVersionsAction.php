<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class ConnectPlatformVersionsAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords      = 0;
            $existingPlatformIds = [];
            $existingVersionIds  = [];

            DB::transaction(function () use (&$existingPlatformIds, &$existingVersionIds) {
                $existingPlatformIds = DB::table('g_platforms')->pluck('id')->toArray();
                $existingVersionIds  = DB::table('g_platform_versions')->pluck('id')->toArray();
            });

            $newRecords = [];

            collect($records)->map(function ($record) use ($existingPlatformIds, $existingVersionIds, &$newRecords) {
                if (!in_array($record['id'], $existingPlatformIds)) {
                    return null;
                }

                foreach ($record['versions'] as $version) {
                    if (!in_array($version, $existingVersionIds)) {
                        continue;
                    }

                    $newRecords[] = [
                        'id'            => $version,
                        'g_platform_id' => $record['id'],
                    ];
                }
            });

            collect($newRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                DB::transaction(function () use ($chunk, &$writtenRecords) {
                    foreach ($chunk as $record) {
                        $result         = DB::table('g_platform_versions')->where('id', $record['id'])->update(['g_platform_id' => $record['g_platform_id']]);
                        $writtenRecords += $result;
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
