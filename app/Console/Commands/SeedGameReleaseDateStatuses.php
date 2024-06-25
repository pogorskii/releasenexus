<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGameReleaseDateStatusesAction;
use App\Jobs\SeedGameReleaseDateStatusesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedGameReleaseDateStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-game-release-date-statuses';
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
            $this->info('Seeding all game release date statuses from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedGameReleaseDateStatusesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGameReleaseDateStatusesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game release date statuses from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game release date statuses from IGDB: '.$e->getMessage());
        }
    }
}
