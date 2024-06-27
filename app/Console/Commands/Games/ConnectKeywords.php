<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchAction;
use App\Jobs\Games\ConnectKeywordsJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectKeywords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-keywords';
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
            $this->info('Connecting all game keywords from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectKeywordsJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchAction::execute($chunkNumber, 'id asc', ['id, keywords'], 2000, 'keywords != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game keywords from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game keywords from IGDB: '.$e->getMessage());
        }
    }
}
