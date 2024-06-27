<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchGamesAction;
use App\Jobs\Games\ConnectCollectionsFromGamesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectCollectionsFromGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-collections-from-games';
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
            $this->info('Connecting all game collections from games from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectCollectionsFromGamesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGamesAction::execute($chunkNumber, 'id asc', ['id, collection, collections'], 2000, 'collection != null | collections != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game collections from games from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game collections from games from IGDB: '.$e->getMessage());
        }
    }
}
