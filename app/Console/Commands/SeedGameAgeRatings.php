<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGameAgeRatingsAction;
use App\Jobs\SeedGameAgeRatingsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedGameAgeRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-game-age-ratings';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->info('Seeding all game age ratings from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedGameAgeRatingsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGameAgeRatingsAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game age ratings from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game age ratings from IGDB: '.$e->getMessage());
        }
    }
}
