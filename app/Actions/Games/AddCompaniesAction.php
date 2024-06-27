<?php

namespace App\Actions\Games;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AddCompaniesAction
{
    public static function execute(array $records): array
    {
        try {
            $tableName                  = 'g_companies';
            $localIdsName               = 'id';
            $morphTableName             = 'g_companiables';
            $writtenRecords             = 0;
            $skippedRecords             = 0;
            $existingRecordsIds         = [];
            $existingCompanyWebsitesIds = [];
            $existingWebsites           = [];
            $existingCompanyLogosIds    = [];
            $existingImageables         = [];

            DB::transaction(function () use (&$existingRecordsIds, &$existingCompanyLogosIds, &$existingCompanyWebsitesIds, &$existingImageables, $tableName, $localIdsName, &$existingWebsites) {
                $existingRecordsIds         = DB::table($tableName)->pluck($localIdsName)->toArray();
                $existingCompanyWebsitesIds = DB::table('g_websites')->where('websiteable_type', 'App\Models\GCompany')->pluck('origin_id')->toArray();
                $existingWebsites           = DB::table('g_websites')->where('websiteable_type', 'App\Models\GCompany')->get()->toArray();
                $existingCompanyLogosIds    = DB::table('g_images')->where('collection', 'company_logos')->pluck('image_id')->toArray();
                $existingImageables         = DB::table('g_imageables')->where('imageable_type', 'App\Models\GCompany')->get()->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $imageablesRecords = [];
            $websitesRecords   = [];

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$imageablesRecords, &$websitesRecords, $existingCompanyLogosIds, $existingImageables, $existingCompanyWebsitesIds, $existingWebsites) {
                if (array_key_exists('logo', $record) && in_array($record['logo'], $existingCompanyLogosIds)) {
                    $imageablesRecords[] = [
                        'g_image_id'     => $existingImageables[array_search($record['logo'], $existingCompanyLogosIds)]->image_id,
                        'imageable_id'   => $record['id'],
                        'imageable_type' => 'App\Models\GCompany',
                        'collection'     => 'company_logos',
                        'created_at'     => Carbon::now(),
                        'updated_at'     => Carbon::now(),
                    ];
                }

                if (array_key_exists('websites', $record)) {
                    foreach ($record['websites'] as $website) {
                        if (!in_array($website, $existingCompanyWebsitesIds)) {
                            continue;
                        }

                        $websiteRecord = $existingWebsites[array_search($website, $existingCompanyWebsitesIds)];

                        $websitesRecords[] = [
                            'origin_id'        => $website,
                            'category'         => $websiteRecord->category,
                            'checksum'         => $websiteRecord->checksum,
                            'trusted'          => $websiteRecord->trusted,
                            'url'              => $websiteRecord->url,
                            'websiteable_id'   => $record['id'],
                            'websiteable_type' => 'App\Models\GCompany',
                            'created_at'       => Carbon::now(),
                            'updated_at'       => Carbon::now(),
                        ];
                    }
                }

//                return [
//                    $localIdsName   => $record['id'],
//                    'collection'    => 'company_logos',
//                    'alpha_channel' => $record['alpha_channel'] ?? false,
//                    'animated'      => $record['animated'] ?? false,
//                    'checksum'      => $record['checksum'],
//                    'height'        => $record['height'] ?? null,
//                    'image_id'      => $record['image_id'],
//                    'url'           => $record['url'],
//                    'width'         => $record['width'] ?? null,
//                    'created_at'    => Carbon::now(),
//                    'updated_at'    => Carbon::now(),
//                ];
            })->toArray();

            $result = DB::table($tableName)->insert($transformedRecords);
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

            $result = DB::table($morphTableName)->insert($imageablesRecords);
            if ($result) {
                $writtenRecords += count($imageablesRecords);
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
