<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchGamesAction;
use App\Jobs\Games\ConnectAgeRatingsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectAgeRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-age-ratings';
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
            $this->info('Connecting all game age ratings from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectAgeRatingsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGamesAction::execute($chunkNumber, 'id asc', ['id, age_ratings'], 2000, 'age_ratings != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game age ratings from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game age ratings from IGDB: '.$e->getMessage());
        }
    }
}
