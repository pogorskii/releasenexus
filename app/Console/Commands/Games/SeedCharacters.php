<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchCharactersAction;
use App\Jobs\Games\SeedCharactersJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedCharacters extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-characters';
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
            $this->info('Seeding all game characters from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedCharactersJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchCharactersAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game characters from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game characters from IGDB: '.$e->getMessage());
        }
    }
}
