<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class ConnectPlatformVersionReleaseDatesAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords             = 0;
            $skippedRecords             = 0;
            $existingPlatformVersionIds = [];
            $existingReleaseDates       = collect();

            DB::transaction(function () use (&$existingPlatformVersionIds, &$existingReleaseDates) {
                $existingPlatformVersionIds = DB::table('g_platform_versions')->pluck('id')->toArray();
                $existingReleaseDates       = DB::table('g_release_dates')->where('dateable_type', 'App\Models\GPlatformVersion')->get();
            });

            $existingReleaseDateIds = $existingReleaseDates->pluck('origin_id')->toArray();

            $pivotRecords = [];

            collect($records)->map(function ($record) use ($existingPlatformVersionIds, $existingReleaseDates, $existingReleaseDateIds, &$pivotRecords) {
                if (!in_array($record['id'], $existingPlatformVersionIds)) {
                    return null;
                }

                foreach ($record['platform_version_release_dates'] as $releaseDate) {
                    if (!in_array($releaseDate, $existingReleaseDateIds)) {
                        continue;
                    }

                    $existingReleaseDate = $existingReleaseDates->where('origin_id', $releaseDate)->first();

                    $pivotRecords[] = [
                        'dateable_id' => $record['id'],
                        'origin_id'   => $existingReleaseDate->origin_id,
                    ];
                }
            });

            collect($pivotRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                foreach ($chunk as $record) {
                    $result         = DB::table('g_release_dates')->where('dateable_type', 'App\Models\GPlatformVersion')->where('origin_id', $record['origin_id'])->update(['dateable_id' => $record['dateable_id']]);
                    $writtenRecords += $result;
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
