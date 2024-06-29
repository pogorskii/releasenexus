<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class AddPlatformVersionsAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName                = 'g_platform_versions';
            $localIdsName             = 'id';
            $writtenRecords           = 0;
            $skippedRecords           = 0;
            $existingRecordsIds       = [];
            $existingCompaniesIds     = [];
            $existingPlatformLogosIds = [];
            $existingImageables       = [];

            $recordsIds = collect($records)->pluck('id')->toArray();

            DB::transaction(function () use ($recordsIds, &$existingRecordsIds, &$existingCompaniesIds, &$existingPlatformLogosIds, &$existingImageables, $tableName, $localIdsName) {
                $existingRecordsIds       = DB::table($tableName)->whereIn($localIdsName, $recordsIds)->pluck($localIdsName)->toArray();
                $existingCompaniesIds     = DB::table('g_companies')->pluck('id')->toArray();
                $existingPlatformLogosIds = DB::table('g_images')->where('collection', 'platform_logos')->pluck('origin_id')->toArray();
                $existingImageables       = DB::table('g_imageables')->where('imageable_type', 'App\Models\GPlatformVersion')->get()->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $companiableRecords = [];
            $imageableRecords   = [];

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$companiableRecords, &$imageableRecords, $existingCompaniesIds, $existingPlatformLogosIds, $existingImageables) {
                if (array_key_exists('companies', $record)) {
                    foreach ($record['companies'] as $company) {
                        if (in_array($company, $existingCompaniesIds)) {
                            $companiableRecords[] = [
                                'g_company_id'     => $company,
                                'companiable_id'   => $record['id'],
                                'companiable_type' => 'App\Models\GPlatformVersion',
                                'role'             => 'developer',
                            ];
                        }
                    }
                }

                if (array_key_exists('platform_logo', $record) && in_array($record['platform_logo'], $existingPlatformLogosIds)) {
                    $existingImageable = $existingImageables[array_search($record['platform_logo'], $existingPlatformLogosIds)];

                    $imageableRecords[] = [
                        'g_image_id'     => $existingImageable->g_image_id,
                        'imageable_id'   => $record['id'],
                        'imageable_type' => 'App\Models\GPlatformVersion',
                        'collection'     => 'platform_logos',
                        'updated_at'     => now(),
                        'created_at'     => now(),
                    ];
                }

                return [
                    $localIdsName  => $record['id'],
                    'checksum'     => $record['checksum'],
                    'connectivity' => $record['connectivity'] ?? null,
                    'cpu'          => $record['cpu'] ?? null,
                    'graphics'     => $record['graphics'] ?? null,
                    'media'        => $record['media'] ?? null,
                    'memory'       => $record['memory'] ?? null,
                    'name'         => $record['name'],
                    'os'           => $record['os'] ?? null,
                    'output'       => $record['output'] ?? null,
                    'resolutions'  => $record['resolutions'] ?? null,
                    'slug'         => $record['slug'],
                    'sound'        => $record['sound'] ?? null,
                    'storage'      => $record['storage'] ?? null,
                    'summary'      => $record['summary'] ?? null,
                    'url'          => $record['url'] ?? null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];
            })->toArray();

            $result = DB::table($tableName)->insert($transformedRecords);
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

            collect($companiableRecords)->chunk(1000)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('g_companiables')->insert($chunk->toArray());
                if ($result) {
                    $writtenRecords += count($chunk);
                }
            });

            $result         = DB::table('g_imageables')->upsert($imageableRecords, [
                'g_image_id',
                'imageable_id',
                'imageable_type',
                'collection',
            ], ['imageable_id', 'updated_at']);
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
