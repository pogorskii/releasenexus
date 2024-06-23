<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGameCoversAction;
use App\Actions\Games\FetchGameReleaseDatesAction;
use App\Jobs\SeedGameCoversJob;
use App\Jobs\SeedGameReleaseDatesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedGameCovers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-game-covers';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Seeding all game covers from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedGameCoversJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGameCoversAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game covers from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game covers from IGDB: '.$e->getMessage());
        }
    }
}
