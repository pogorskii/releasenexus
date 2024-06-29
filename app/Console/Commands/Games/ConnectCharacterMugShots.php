<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchCharactersAction;
use App\Jobs\Games\ConnectCharacterMugShotsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectCharacterMugShots extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-character-mug-shots';
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
            $this->info('Connecting all game character mug shots from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectCharacterMugShotsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchCharactersAction::execute($chunkNumber, 'id asc', ['id, mug_shot'], 2000, 'mug_shot != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game character mug shots from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game character mug shots from IGDB: '.$e->getMessage());
        }
    }
}
