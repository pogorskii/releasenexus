<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGameKeywordsAction;
use App\Jobs\SeedGameKeywordsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedGameKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-game-keywords';
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
            $this->info('Seeding all game keywords from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedGameKeywordsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGameKeywordsAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game keywords from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game keywords from IGDB: '.$e->getMessage());
        }
    }
}
