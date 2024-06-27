<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchCollectionMembershipsAction;
use App\Jobs\Games\ConnectCollectionsFromMembershipsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectCollectionsFromMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-collections-from-memberships';
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
                $job = new ConnectCollectionsFromMembershipsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchCollectionMembershipsAction::execute($chunkNumber, 'id asc', ['id, game, collection'], 2000, 'game != null & collection != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game collections from memberships from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game collections from memberships from IGDB: '.$e->getMessage());
        }
    }
}
