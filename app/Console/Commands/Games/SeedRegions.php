<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchRegionsAction;
use App\Jobs\Games\SeedRegionsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedRegions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-regions';
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
            $this->info('Seeding all game regions from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedRegionsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchRegionsAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game regions from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game regions from IGDB: '.$e->getMessage());
        }
    }
}
