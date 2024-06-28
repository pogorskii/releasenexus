<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchInvolvedCompaniesAction;
use App\Jobs\Games\ConnectCompaniesFromInvolvedCompaniesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class ConnectCompaniesFromInvolvedCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:connect-companies-from-involved-companies';
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
            $this->info('Connecting all game companies from involved companies from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new ConnectCompaniesFromInvolvedCompaniesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchInvolvedCompaniesAction::execute($chunkNumber, 'id asc')) > 0);

            $this->newLine();
            $this->info('Finished connecting all game companies from involved companies from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while connecting all game companies from involved companies from IGDB: '.$e->getMessage());
        }
    }
}
