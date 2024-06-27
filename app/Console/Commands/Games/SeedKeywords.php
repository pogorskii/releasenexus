<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchKeywordsAction;
use App\Jobs\Games\SeedKeywordsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-keywords';
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
                $job = new SeedKeywordsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchKeywordsAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game keywords from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game keywords from IGDB: '.$e->getMessage());
        }
    }
}
