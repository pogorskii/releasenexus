<?php

namespace App\Console\Commands;

use App\Actions\Games\AddGamesToDBAction;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Process\Pool;
use Illuminate\Support\Facades\Process;

class SeedGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:seed';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(int $iterations = 10): void
    {
        try {
            $this->info('Seeding games into the database...');
            $pool = Process::concurrently(function (Pool $pool) use ($iterations) {
                for ($i = 0; $i < $iterations; $i++) {
                    $pool->as($i)->command('php artisan igdb:fetch-games'.' '.$i);
                }
            });

            $this->info('Games fetched from IGDB.');

//            // Merge all the games into one array
//            $mergedGames = [];
//            foreach ($pool as $poolResult) {
//                $mergedGames = array_merge($mergedGames, $poolResult->output());
//            }
//
//            $totalGames = collect($mergedGames)->count();
            $totalGames = collect($pool[0]->output())->count();

            ray($pool[1]->output());

            $this->info($pool[0]->output());

            $this->info("{$totalGames} games fetched from IGDB.");

//            AddGamesToDBAction::execute($pool[0]->output());

            $this->info('Games successfully added to the DB.');
        } catch (Exception $e) {
            $this->error('An error occurred while dumping games into the database.');
            // Log error
        }
    }
}
