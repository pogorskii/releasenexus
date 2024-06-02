<?php

namespace App\Console\Commands;

use App\Actions\Games\IGDB\FetchGamesFromIGDBAction;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Http\Client\ConnectionException;

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
     * @throws ConnectionException
     */
    public function handle(): array
    {
        $this->info('Fetching games from IGDB...');
        $iteration = $this->argument('iteration');
        $games     = FetchGamesFromIGDBAction::execute($iteration);

//            $this->info("{$games['written']} games successfully fetched from IGDB, {$games['skipped']} games skipped.");

        $totalGames = collect($games)->count();
        $this->info("{$totalGames} Games fetched from IGDB.");

//        ray($games);

//        $this['games'] = $games;
        $this->games = $games;

        return $games;
//        $this->error('An error occurred while dumping games into the database.');
        // Log error
    }
}