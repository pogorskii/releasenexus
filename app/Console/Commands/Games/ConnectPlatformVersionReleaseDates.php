<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchPlatformsAction;
use App\Actions\Games\FetchPlatformVersionsAction;
use App\Jobs\Games\ConnectPlatformVersionReleaseDatesJob;
use App\Jobs\Games\ConnectPlatformVersionsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectPlatformVersionReleaseDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-platform-version-release-dates';
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
            $this->info('Connecting all game platform version release dates from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectPlatformVersionReleaseDatesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchPlatformVersionsAction::execute($chunkNumber, 'id asc', ['id, platform_version_release_dates'], 2000, 'platform_version_release_dates != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game platform version release dates from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game platform version release dates from IGDB: '.$e->getMessage());
        }
    }
}
