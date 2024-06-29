<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchCompaniesAction;
use App\Actions\Games\FetchGamesAction;
use App\Jobs\Games\ConnectCompaniesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-companies';
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
            $this->info('Connecting all game companies from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectCompaniesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchCompaniesAction::execute($chunkNumber, 'id asc', ['id, changed_company_id, parent'], 2000, 'changed_company_id != null | parent != null')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game companies from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game companies from IGDB: '.$e->getMessage());
        }
    }
}
