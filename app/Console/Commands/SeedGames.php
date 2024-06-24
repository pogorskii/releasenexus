<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGamesAction;
use App\Jobs\SeedGamesJob;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedGames extends Command
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

            $chunkNumber = 0;
            do {
                $job = new SeedGamesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGamesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all games from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all games from IGDB: '.$e->getMessage());
        }
    }
}
