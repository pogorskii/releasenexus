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
            $writtenRecords             = 0;
            $skippedRecords             = 0;
            $existingRecordsIds         = [];
            $existingCompanyWebsitesIds = [];
            $existingWebsites           = [];
            $existingCompanyLogosIds    = [];
            $existingImageables         = [];
            $existingGamesIds           = [];

            DB::transaction(function () use (&$existingRecordsIds, &$existingCompanyLogosIds, &$existingCompanyWebsitesIds, &$existingImageables, &$existingGamesIds, $tableName, $localIdsName, &$existingWebsites) {
                $existingRecordsIds         = DB::table($tableName)->pluck($localIdsName)->toArray();
                $existingCompanyWebsitesIds = DB::table('g_websites')->where('websiteable_type', 'App\Models\GCompany')->pluck('origin_id')->toArray();
                $existingWebsites           = DB::table('g_websites')->where('websiteable_type', 'App\Models\GCompany')->get()->toArray();
                $existingCompanyLogosIds    = DB::table('g_images')->where('collection', 'company_logos')->pluck('origin_id')->toArray();
                $existingImageables         = DB::table('g_imageables')->where('imageable_type', 'App\Models\GCompany')->get()->toArray();
                $existingGamesIds           = DB::table('games')->pluck('origin_id')->toArray();
            });

            $newRecords = array_filter($records, function ($record) use ($existingRecordsIds, &$skippedRecords) {
                if (in_array($record['id'], $existingRecordsIds)) {
                    $skippedRecords++;

                    return false;
                }

                return true;
            });

            $imageablesRecords          = [];
            $websitesRecords            = [];
            $gameInvolvedCompanyRecords = [];

            $transformedRecords = collect($newRecords)->map(function ($record) use ($localIdsName, &$imageablesRecords, &$websitesRecords, &$gameInvolvedCompanyRecords, $existingCompanyLogosIds, $existingImageables, $existingCompanyWebsitesIds, $existingWebsites, $existingGamesIds) {
                if (array_key_exists('logo', $record) && in_array($record['logo'], $existingCompanyLogosIds)) {
                    $existingImageable = $existingImageables[array_search($record['logo'], $existingCompanyLogosIds)];

                    $imageablesRecords[] = [
                        'g_image_id'     => $existingImageable->g_image_id,
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

                if (array_key_exists('developed', $record)) {
                    foreach ($record['developed'] as $game) {
                        if (!in_array($game, $existingGamesIds)) {
                            continue;
                        }

                        $gameInvolvedCompanyRecords[] = [
                            'g_company_id'     => $record['id'],
                            'companiable_id'   => $game,
                            'companiable_type' => 'App\Models\Game',
                            'role'             => 'developer',
                            'created_at'       => Carbon::now(),
                            'updated_at'       => Carbon::now(),
                        ];
                    }
                }

                if (array_key_exists('published', $record)) {
                    foreach ($record['published'] as $game) {
                        if (!in_array($game, $existingGamesIds)) {
                            continue;
                        }

                        $gameInvolvedCompanyRecords[] = [
                            'g_company_id'     => $record['id'],
                            'companiable_id'   => $game,
                            'companiable_type' => 'App\Models\Game',
                            'role'             => 'publisher',
                            'created_at'       => Carbon::now(),
                            'updated_at'       => Carbon::now(),
                        ];
                    }
                }

                return [
                    $localIdsName          => $record['id'],
                    'change_date'          => array_key_exists('change_date', $record) ? Carbon::createFromTimestamp($record['change_date'])->toDateTimeString() : null,
                    'change_date_category' => array_key_exists('change_date_category', $record) ? number_format($record['change_date_category'], 0, '', '') : null,
                    'checksum'             => $record['checksum'],
                    'country'              => $record['country'] ?? null,
                    'description'          => $record['description'] ?? null,
                    'name'                 => $record['name'],
                    'slug'                 => $record['slug'],
                    'start_date'           => array_key_exists('start_date', $record) ? Carbon::createFromTimestamp($record['start_date'])->toDateTimeString() : null,
                    'start_date_category'  => array_key_exists('start_date_category', $record) ? number_format($record['start_date_category'], 0, '', '') : null,
                    'url'                  => $record['url'] ?? null,
                    'created_at'           => Carbon::now(),
                    'updated_at'           => Carbon::now(),
                ];
            })->toArray();

            $result = DB::table($tableName)->insert($transformedRecords);
            if ($result) {
                $writtenRecords += count($transformedRecords);
            }

            $result = DB::table('g_imageables')->upsert($imageablesRecords, [
                'g_image_id',
                'imageable_id',
                'imageable_type',
            ], ['imageable_id']);
            if ($result) {
                $writtenRecords += count($imageablesRecords);
            }

            $result = DB::table('g_websites')->upsert($websitesRecords, [
                'origin_id',
                'websiteable_id',
                'websiteable_type',
            ], ['websiteable_id']);
            if ($result) {
                $writtenRecords += count($websitesRecords);
            }

            collect($gameInvolvedCompanyRecords)->chunk(1000)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                $result = DB::table('g_companiables')->insert($chunk->toArray());
                if ($result) {
                    $writtenRecords += count($chunk);
                } else {
                    $skippedRecords += count($chunk);
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
