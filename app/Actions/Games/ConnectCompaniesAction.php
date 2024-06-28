<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class ConnectCompaniesAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords       = 0;
            $existingCompaniesIds = DB::table('g_companies')->pluck('id')->toArray();

            $connectRecords = [];

            collect($records)->map(function ($record) use (&$connectRecords, $existingCompaniesIds) {
                if (array_key_exists('changed_company_id', $record) && in_array($record['changed_company_id'], $existingCompaniesIds)) {
                    $connectRecords[] = [
                        'id'                 => $record['id'],
                        'changed_company_id' => $record['changed_company_id'],
                    ];
                }

                if (array_key_exists('parent', $record) && in_array($record['parent'], $existingCompaniesIds)) {
                    $connectRecords[] = [
                        'id'                => $record['id'],
                        'parent_company_id' => $record['parent'],
                    ];
                }
            });

            collect($connectRecords)->chunk(500)->each(function ($chunk) use (&$writtenRecords, &$skippedRecords) {
                DB::transaction(function () use ($chunk, &$writtenRecords, &$skippedRecords) {
                    $chunk->each(function ($record) use (&$writtenRecords, &$skippedRecords) {
                        $result = DB::table('g_companies')->where('id', $record['id'])->update($record);
                        if ($result) {
                            $writtenRecords++;
                        } else {
                            $skippedRecords++;
                        }
                    });
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
