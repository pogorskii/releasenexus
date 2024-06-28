<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchNetworkTypesAction;
use App\Jobs\Games\SeedNetworkTypesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedNetworkTypes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-network-types';
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
            $this->info('Seeding all game network types from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedNetworkTypesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchNetworkTypesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game network types from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game network types from IGDB: '.$e->getMessage());
        }
    }
}
