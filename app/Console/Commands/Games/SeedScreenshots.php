<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchScreenshotsAction;
use App\Jobs\Games\SeedScreenshotsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedScreenshots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-screenshots';
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
            $this->info('Seeding all game screenshots from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedScreenshotsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchScreenshotsAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game screenshots from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game screenshots from IGDB: '.$e->getMessage());
        }
    }
}
