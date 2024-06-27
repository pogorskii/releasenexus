<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchAction;
use App\Jobs\Games\ConnectGenresJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectGenres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-genres';
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
            $this->info('Connecting all game genres from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectGenresJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchAction::execute($chunkNumber, 'id asc', ['id, genres'], 2000, 'genres != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game genres from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game genres from IGDB: '.$e->getMessage());
        }
    }
}
