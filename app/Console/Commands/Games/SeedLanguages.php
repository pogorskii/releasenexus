<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchLanguagesAction;
use App\Jobs\Games\SeedLanguagesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedLanguages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-languages';
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
            $this->info('Seeding all game languages from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedLanguagesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchLanguagesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game languages from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game languages from IGDB: '.$e->getMessage());
        }
    }
}
