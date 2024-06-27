<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchLanguageSupportsAction;
use App\Jobs\Games\SeedLanguageSupportsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedLanguageSupports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-language-supports';
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
            $this->info('Seeding all game language supports from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedLanguageSupportsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchLanguageSupportsAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game language supports from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game language supports from IGDB: '.$e->getMessage());
        }
    }
}
