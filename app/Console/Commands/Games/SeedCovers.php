<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchCoversAction;
use App\Jobs\Games\SeedCoversJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedCovers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-covers';
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
            $this->info('Seeding all game covers from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedCoversJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchCoversAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game covers from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game covers from IGDB: '.$e->getMessage());
        }
    }
}
