<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGamePlatformsAction;
use App\Jobs\SeedGamePlatformsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedGamePlatforms extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-game-platforms';
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
            $this->info('Seeding all game platforms from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedGamePlatformsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGamePlatformsAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game platforms from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game platforms from IGDB: '.$e->getMessage());
        }
    }
}
