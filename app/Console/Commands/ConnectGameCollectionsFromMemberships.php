<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGameCollectionMembershipsAction;
use App\Jobs\ConnectGameCollectionsFromMembershipsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectGameCollectionsFromMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-game-collections-from-memberships';
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
            $this->info('Connecting all game collections from memberships from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectGameCollectionsFromMembershipsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGameCollectionMembershipsAction::execute($chunkNumber, 'id asc', ['id, game, collection'], 2000, 'game != null & collection != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game collections from memberships from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game collections from memberships from IGDB: '.$e->getMessage());
        }
    }
}
