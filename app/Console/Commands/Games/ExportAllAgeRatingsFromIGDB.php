<?php

namespace App\Console\Commands\Games;

use App\Jobs\Games\ExportAgeRatingsJob;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Throwable;

class ExportAllAgeRatingsFromIGDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:dump-age-ratings';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches all age ratings from IGDB and exports them to a CSV file.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->info('Exporting all age ratings from IGDB...');
            $filename = "age-ratings-dump-".now()->format('Ymd-U').".csv";
            $path     = storage_path('app/public/igdb/'.$filename);

            $jobs = [];
            for ($i = 0; $i < 80; $i++) {
                $jobs[] = new ExportAgeRatingsJob($i, $path);
            }

            $this->withProgressBar($jobs, fn($job) => Bus::dispatch($job));
            $this->newLine();
            $this->info('Finished exporting all age ratings from IGDB.');
        } catch (Exception|Throwable $e) {
            $this->error('An error occurred while exporting all age ratings from IGDB: '.$e->getMessage());
        }
    }
}
