<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGamePlayerPerspectivesAction;
use App\Actions\Games\FetchGamesAction;
use App\Jobs\ConnectGamePlayerPerspectivesJob;
use App\Jobs\SeedGamePlayerPerspectivesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectGamePlayerPerspectives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-game-player-perspectives';
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
            $this->info('Connecting all game characters from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectGamePlayerPerspectivesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGamesAction::execute($chunkNumber, 'id asc', ['id'], 'player_perspectives != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game characters from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game characters from IGDB: '.$e->getMessage());
        }
    }
}
