<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchEventsAction;
use App\Jobs\Games\SeedEventsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-events';
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
            $this->info('Seeding all game events from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedEventsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchEventsAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game events from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game events from IGDB: '.$e->getMessage());
        }
    }
}
