<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchGamesAction;
use App\Jobs\Games\ConnectThemesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectThemes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-themes';
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
            $this->info('Connecting all game themes from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectThemesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGamesAction::execute($chunkNumber, 'id asc', ['id, themes'], 2000, 'themes != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game themes from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game themes from IGDB: '.$e->getMessage());
        }
    }
}
