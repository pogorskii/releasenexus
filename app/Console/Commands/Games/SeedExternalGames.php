<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchExternalGamesAction;
use App\Jobs\Games\SeedExternalGamesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedExternalGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-external-games';
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
            $this->info('Seeding all external games from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedExternalGamesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchExternalGamesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all external games from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all external games from IGDB: '.$e->getMessage());
        }
    }
}
