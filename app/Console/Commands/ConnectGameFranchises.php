<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGamesAction;
use App\Jobs\ConnectGameFranchisesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectGameFranchises extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-game-franchises';
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
            $this->info('Connecting all game franchises from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectGameFranchisesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGamesAction::execute($chunkNumber, 'id asc', ['id, franchise, franchises'], 2000, 'franchise != null | franchises != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game franchises from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game franchises from IGDB: '.$e->getMessage());
        }
    }
}
