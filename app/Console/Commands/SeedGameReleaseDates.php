<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGameReleaseDatesAction;
use App\Jobs\SeedGameReleaseDatesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedGameReleaseDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-game-release-dates';
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
            $this->info('Seeding all game release dates from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedGameReleaseDatesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGameReleaseDatesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game release dates from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game release dates from IGDB: '.$e->getMessage());
        }
    }
}