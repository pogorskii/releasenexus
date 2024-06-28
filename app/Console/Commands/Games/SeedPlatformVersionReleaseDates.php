<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchPlatformVersionReleaseDatesAction;
use App\Jobs\Games\SeedPlatformVersionReleaseDatesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedPlatformVersionReleaseDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-platform-version-release-dates';
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
            $this->info('Seeding all game platform version release dates from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedPlatformVersionReleaseDatesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchPlatformVersionReleaseDatesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game platform version release dates from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game platform version release dates from IGDB: '.$e->getMessage());
        }
    }
}
