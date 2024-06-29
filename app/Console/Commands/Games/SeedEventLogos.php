<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchEventLogosAction;
use App\Jobs\Games\SeedEventLogosJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedEventLogos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-event-logos';
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
            $this->info('Seeding all game event logos from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedEventLogosJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchEventLogosAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game event logos from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game event logos from IGDB: '.$e->getMessage());
        }
    }
}
