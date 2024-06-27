<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchAction;
use Illuminate\Console\Command;

class FetchGamesFromIGDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:fetch-games {iteration}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches games from IGDB and adds them to the database.';

    /**
     * Execute the console command.
     */
    public function handle(): array
    {
        $this->info('Fetching games from IGDB...');
        $iteration  = $this->argument('iteration');
        $games      = FetchAction::execute($iteration);
        $totalGames = collect($games)->count();
        $this->info("{$totalGames} games fetched from IGDB.");

        return $games;
    }
}
