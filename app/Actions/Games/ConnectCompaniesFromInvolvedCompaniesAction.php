<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\DB;

class ConnectCompaniesFromInvolvedCompaniesAction
{
    public static function execute(array $records): array
    {
        try {
            $writtenRecords       = 0;
            $skippedRecords       = 0;
            $existingCompaniesIds = [];
            $existingGamesIds     = [];

            DB::transaction(function () use (&$existingCompaniesIds, &$existingGamesIds) {
                $existingCompaniesIds = DB::table('g_companies')->pluck('id')->toArray();
                $existingGamesIds     = DB::table('games')->pluck('id')->toArray();
            });

            $connectRecords = [];

            collect($records)->map(function ($record) use (&$connectRecords, $existingCompaniesIds, $existingGamesIds) {
                if (!array_key_exists('company', $record) || !in_array($record['company'], $existingCompaniesIds) || !array_key_exists('game', $record) || !in_array($record['game'], $existingGamesIds)) {
                    return null;
                }

                $role = $record['developer'] ? 'developer' : ($record['publisher'] ? 'publisher' : ($record['supporting'] ? 'supporting' : 'porting'));

                $connectRecords[] = [
                    'g_company_id'     => $record['company'],
                    'companiable_id'   => $record['game'],
                    'companiable_type' => 'App\Models\Game',
                    'role'             => $role,
                    'updated_at'       => now(),
                    'created_at'       => now(),
                ];
            });

            $result         = DB::table('g_companiables')->upsert($connectRecords, [
                'g_company_id',
                'companiable_id',
                'companiable_type',
                'role',
            ], ['role', 'updated_at', 'created_at']);
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
