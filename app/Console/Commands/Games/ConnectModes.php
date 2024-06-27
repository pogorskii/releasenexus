<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchGamesAction;
use App\Jobs\Games\ConnectModesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectModes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-modes-job';
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
            $this->info('Connecting all game modes from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectModesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGamesAction::execute($chunkNumber, 'id asc', ['id, game_modes'], 2000, 'game_modes != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game modes from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game modes from IGDB: '.$e->getMessage());
        }
    }
}
