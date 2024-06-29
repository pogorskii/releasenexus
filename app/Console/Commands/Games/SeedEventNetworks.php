<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchEventNetworksAction;
use App\Jobs\Games\SeedEventNetworksJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedEventNetworks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-event-networks';
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
            $this->info('Seeding all game event networks from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedEventNetworksJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchEventNetworksAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game event networks from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game event networks from IGDB: '.$e->getMessage());
        }
    }
}
