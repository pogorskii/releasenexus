<?php

namespace Database\Seeders;

use App\Actions\GetRecordsFromCSVAction;
use App\Models\Game;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use League\Csv\Exception;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @throws Exception
     */
    public function run(): void
    {
        $result  = GetRecordsFromCSVAction::execute('app/public/igdb/games-dump.csv');
        $records = $result['records'];

        DB::transaction(function () use ($records) {
            foreach ($records as $offset => $record) {
                Game::create([
                    'origin_id'               => (int)$record['id'],
                    'name'                    => $record['name'],
                    'slug'                    => $record['slug'],
                    'aggregated_rating'       => (double)$record['aggregated_rating'],
                    'aggregated_rating_count' => (int)$record['aggregated_rating_count'],
                    'hypes'                   => (int)$record['hypes'],
                    'status'                  => (int)$record['status'],
                    'summary'                 => $record['summary'],
                    'synced_at'               => now(),
                ]);
            }
        });
    }
}
