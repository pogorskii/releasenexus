<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGameThemesAction;
use App\Jobs\SeedGameThemesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedGameThemes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-game-themes';
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
            $this->info('Seeding all game themes from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedGameThemesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGameThemesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game themes from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game themes from IGDB: '.$e->getMessage());
        }
    }
}
