<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchPlatformsAction;
use App\Jobs\Games\ConnectPlatformVersionsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectPlatformVersions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-platform-versions';
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
            $this->info('Connecting all game platform versions from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectPlatformVersionsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchPlatformsAction::execute($chunkNumber, 'id asc', ['id, versions'], 2000, 'versions != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game platform versions from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game platform versions from IGDB: '.$e->getMessage());
        }
    }
}
