<?php

namespace App\Console\Commands;

use App\Jobs\SeedGamesFromIGDBJob;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedGamesFromIGDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-games';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches games from IGDB and adds them to the database.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->info('Seeding all games from IGDB...');

            $jobs = [];
            for ($i = 0; $i < 150; $i++) {
                $jobs[] = new SeedGamesFromIGDBJob($i);
            }

//            $this->withProgressBar($jobs, fn($job) => Bus::dispatch($job));
            Bus::batch($jobs)->catch(function (Batch $batch, \Throwable $e) {
                \Log::error('An error occurred while seeding all games from IGDB: '.$e->getMessage());
            })->then(function (Batch $batch) {
                \Log::info('FROM BUS: Finished seeding all games from IGDB.');
            })->dispatch();
            $this->newLine();
            $this->info('Finished seeding all games from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all games from IGDB: '.$e->getMessage());
        }
    }
}
