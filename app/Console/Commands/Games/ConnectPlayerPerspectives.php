<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchGamesAction;
use App\Jobs\Games\ConnectPlayerPerspectivesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectPlayerPerspectives extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-player-perspectives';
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
            $this->info('Connecting all game player perspectives from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectPlayerPerspectivesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGamesAction::execute($chunkNumber, 'id asc', ['id'], 2000, 'player_perspectives != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game player perspectives from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game player perspectives from IGDB: '.$e->getMessage());
        }
    }
}
