<?php

namespace App\Console\Commands\Games;

use App\Actions\Games\FetchCompanyLogosAction;
use App\Jobs\Games\SeedCompanyLogosJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedCompanyLogos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-company-logos';
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
            $this->info('Seeding all game company logos from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedCompanyLogosJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchCompanyLogosAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game company logos from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game company logos from IGDB: '.$e->getMessage());
        }
    }
}
