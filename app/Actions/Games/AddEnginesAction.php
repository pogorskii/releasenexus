<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddEnginesAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName              = 'g_engines';
            $localIdsName           = 'id';
            $writtenRecords         = 0;
            $skippedRecords         = 0;
            $existingRecordsIds     = [];
            $existingEngineLogosIds = [];
            $existingCompaniesIds   = [];
            $existingPlatformsIds   = [];
            $existingImageables     = [];

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, &$existingEngineLogosIds, &$existingCompaniesIds, &$existingPlatformsIds, &$existingImageables, $tableName, $localIdsName) {
                $existingRecordsIds     = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName)->toArray();
                $existingEngineLogosIds = DB::table('g_images')->where('collection', 'engine_logos')->pluck('origin_id')->toArray();
                $existingImageables     = DB::table('g_imageables')->where('imageable_type', 'App\Models\GEngine')->get()->toArray();
                $existingCompaniesIds   = DB::table('g_companies')->pluck('id')->toArray();
                $existingPlatformsIds   = DB::table('g_platforms')->pluck('id')->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $imageablesRecords  = [];
            $engineablesRecords = [];

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$imageablesRecords, &$engineablesRecords, $existingEngineLogosIds, $existingCompaniesIds, $existingPlatformsIds, $existingImageables) {
                if (array_key_exists('logo', $record) && in_array($record['logo'], $existingEngineLogosIds)) {
                    $existingImageable = $existingImageables[array_search($record['logo'], $existingEngineLogosIds)];

                    $imageablesRecords[] = [
                        'g_image_id'     => $existingImageable->g_image_id,
                        'imageable_id'   => $record['id'],
                        'imageable_type' => 'App\Models\GEngine',
                        'collection'     => 'engine_logos',
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];
                }

                if (array_key_exists('companies', $record)) {
                    foreach ($record['companies'] as $companyId) {
                        if (!in_array($companyId, $existingCompaniesIds)) {
                            continue;
                        }

                        $engineablesRecords[] = [
                            'g_engine_id'     => $record['id'],
                            'engineable_id'   => $companyId,
                            'engineable_type' => 'App\Models\GCompany',
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }
                }

                if (array_key_exists('platforms', $record)) {
                    foreach ($record['platforms'] as $platformId) {
                        if (!in_array($platformId, $existingPlatformsIds)) {
                            continue;
                        }

                        $engineablesRecords[] = [
                            'g_engine_id'     => $record['id'],
                            'engineable_id'   => $platformId,
                            'engineable_type' => 'App\Models\GPlatform',
                            'created_at'      => now(),
                            'updated_at'      => now(),
                        ];
                    }
                }

                return [
                    $localIdsName => $record['id'],
                    'checksum'    => $record['checksum'],
                    'description' => $record['description'] ?? null,
                    'name'        => $record['name'],
                    'slug'        => $record['slug'],
                    'url'         => $record['url'] ?? null,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            })->toArray();

            $result = DB::table($tableName)->insert($transformedRecords);
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

            collect($engineablesRecords)->chunk(1000)->each(function ($chunk) use (&$writtenRecords) {
                $result = DB::table('g_engineables')->upsert($chunk->toArray(), [
                    'g_engine_id',
                    'engineable_id',
                    'engineable_type',
                ], ['engineable_id']);
                if ($result) {
                    $writtenRecords += count($chunk);
                }
            });

            $result         = DB::table('g_imageables')->upsert($imageablesRecords, [
                'g_image_id',
                'imageable_id',
                'imageable_type',
            ], ['imageable_id']);
            $writtenRecords += $result;

            return [
                'written' => $writtenRecords,
                'skipped' => $skippedRecords,
            ];
        } catch (\Throwable $th) {
            dd($th->getMessage());
        }
    }
}
