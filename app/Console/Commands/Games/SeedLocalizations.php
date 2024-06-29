<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchLocalizationsAction;
use App\Jobs\Games\SeedLocalizationsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedLocalizations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-localizations';
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
            $this->info('Seeding all game localizations from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedLocalizationsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchLocalizationsAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game localizations from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game localizations from IGDB: '.$e->getMessage());
        }
    }
}
