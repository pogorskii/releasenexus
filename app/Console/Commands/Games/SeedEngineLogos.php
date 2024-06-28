<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchEngineLogosAction;
use App\Jobs\Games\SeedEngineLogosJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedEngineLogos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-engine-logos';
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
            $this->info('Seeding all game engine logos from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedEngineLogosJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchEngineLogosAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game engine logos from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game engine logos from IGDB: '.$e->getMessage());
        }
    }
}
