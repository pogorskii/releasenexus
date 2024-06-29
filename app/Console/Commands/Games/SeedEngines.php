<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchEnginesAction;
use App\Jobs\Games\SeedEnginesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedEngines extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-engines';
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
            $this->info('Seeding all game engines from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedEnginesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchEnginesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game engines from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game engines from IGDB: '.$e->getMessage());
        }
    }
}
