<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchAgeRatingsAction;
use Illuminate\Console\Command;

class FetchAgeRatingsFromIGDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:fetch-age-ratings {iteration}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches age ratings from IGDB and adds them to the database.';

    /**
     * Execute the console command.
     */
    public function handle(): array
    {
        $this->info('Fetching age ratings from IGDB...');
        $iteration  = $this->argument('iteration');
        $games      = FetchAgeRatingsAction::execute($iteration);
        $totalGames = collect($games)->count();
        $this->info("{$totalGames} age ratings fetched from IGDB.");

        return $games;
    }
}
