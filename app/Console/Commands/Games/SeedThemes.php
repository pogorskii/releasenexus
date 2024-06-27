<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchThemesAction;
use App\Jobs\Games\SeedThemesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedThemes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-themes';
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
                $job = new SeedThemesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchThemesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game themes from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game themes from IGDB: '.$e->getMessage());
        }
    }
}
