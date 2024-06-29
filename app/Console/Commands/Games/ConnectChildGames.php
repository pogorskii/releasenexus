<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchGamesAction;
use App\Jobs\Games\ConnectChildGamesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectChildGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-child-games';
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
            $this->info('Connecting all child games from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectChildGamesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGamesAction::execute($chunkNumber, 'id asc', ['id, parent_game, version_parent, bundles, dlcs, expanded_games, expansions, forks, ports, remakes, remasters, standalone_expansions'], 2000, 'category != 0')) > 0);

            $this->newLine();
            $this->info('Finished connecting all child games from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all child games from IGDB: '.$e->getMessage());
        }
    }
}
