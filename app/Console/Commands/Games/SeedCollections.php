<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchCollectionsAction;
use App\Jobs\Games\SeedCollectionsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedCollections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-collections';
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
            $this->info('Seeding all game collections from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedCollectionsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchCollectionsAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game collections from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game collections from IGDB: '.$e->getMessage());
        }
    }
}
