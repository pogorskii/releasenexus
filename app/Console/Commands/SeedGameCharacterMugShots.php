<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGameCharacterMugShotsAction;
use App\Actions\Games\FetchGameCharactersAction;
use App\Jobs\SeedGameCharacterMugShotsJob;
use App\Jobs\SeedGameCharactersJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedGameCharacterMugShots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-game-character-mug-shots';
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
            $this->info('Seeding all game character mug shots from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedGameCharacterMugShotsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGameCharacterMugShotsAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game character mug shots from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game character mug shots from IGDB: '.$e->getMessage());
        }
    }
}
