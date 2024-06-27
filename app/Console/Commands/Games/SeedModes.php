<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchModesAction;
use App\Jobs\Games\SeedModesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedModes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-modes';
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
            $this->info('Seeding all game modes from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedModesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchModesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game modes from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game modes from IGDB: '.$e->getMessage());
        }
    }
}
