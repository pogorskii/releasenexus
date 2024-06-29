<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchGenresAction;
use App\Jobs\Games\SeedGenresJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedGenres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-genres';
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
            $this->info('Seeding all game genres from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedGenresJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGenresAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game genres from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game genres from IGDB: '.$e->getMessage());
        }
    }
}
