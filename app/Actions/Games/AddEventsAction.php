<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddEventsAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName             = 'g_events';
            $localIdsName          = 'id';
            $writtenRecords        = 0;
            $skippedRecords        = 0;
            $existingRecordsIds    = [];
            $existingGameVideosIds = [];
            $existingGamesIds      = [];

            DB::transaction(function () use (&$existingRecordsIds, &$existingGameVideosIds, &$existingGamesIds, $tableName, $localIdsName) {
                $existingRecordsIds    = DB::table($tableName)->pluck($localIdsName)->toArray();
                $existingGameVideosIds = DB::table('g_videos')->pluck('id')->toArray();
                $existingGamesIds      = DB::table('games')->pluck('origin_id')->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $pivotRecords      = [];
            $videoablesRecords = [];

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$pivotRecords, &$videoablesRecords, $existingGamesIds, $existingGameVideosIds) {
                if (array_key_exists('games', $record)) {
                    foreach ($record['games'] as $game) {
                        if (!in_array($game, $existingGamesIds)) {
                            continue;
                        }

                        $pivotRecords[] = [
                            'g_event_id' => $record['id'],
                            'game_id'    => $game,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                if (array_key_exists('videos', $record)) {
                    foreach ($record['videos'] as $video) {
                        if (!in_array($video, $existingGameVideosIds)) {
                            continue;
                        }

                        $videoablesRecords[] = [
                            'g_video_id'     => $video,
                            'videoable_id'   => $record['id'],
                            'videoable_type' => 'App\Models\GEvent',
                            'created_at'     => now(),
                            'updated_at'     => now(),
                        ];
                    }
                }

                return [
                    $localIdsName     => $record['id'],
                    'checksum'        => $record['checksum'],
                    'description'     => $record['description'] ?? null,
                    'end_time'        => array_key_exists('end_time', $record) ? Carbon::createFromTimestamp($record['end_time']) : null,
                    'live_stream_url' => $record['live_stream_url'] ?? null,
                    'name'            => $record['name'],
                    'slug'            => $record['slug'],
                    'start_time'      => array_key_exists('start_time', $record) ? Carbon::createFromTimestamp($record['start_time']) : null,
                    'time_zone'       => $record['time_zone'] ?? null,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            })->toArray();

            $result = DB::table($tableName)->insert($transformedRecords);
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

            collect($pivotRecords)->chunk(1000)->each(function ($chunk) use (&$writtenRecords) {
                $result = DB::table('game_g_event')->insert($chunk->toArray());
                if ($result) {
                    $writtenRecords += count($chunk);
                }
            });

            collect($videoablesRecords)->chunk(1000)->each(function ($chunk) use (&$writtenRecords) {
                $result         = DB::table('g_videoables')->upsert($chunk->toArray(), [
                    'g_video_id',
                    'videoable_id',
                    'videoable_type',
                ], ['videoable_id', 'created_at', 'updated_at']);
                $writtenRecords += $result;
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
