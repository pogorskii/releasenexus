<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchMultiplayerModesAction;
use App\Jobs\Games\SeedMultiplayerModesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedMultiplayerModes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-multiplayer-modes';
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
            $this->info('Seeding all game multiplayer modes from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedMultiplayerModesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchMultiplayerModesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game multiplayer modes from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game multiplayer modes from IGDB: '.$e->getMessage());
        }
    }
}
