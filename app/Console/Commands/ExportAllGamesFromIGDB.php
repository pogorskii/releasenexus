<?php

namespace App\Console\Commands;

use App\Jobs\ExportGamesFromIGDBJob;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Throwable;

class ExportAllGamesFromIGDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:dump-games';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches all games from IGDB and exports them to a CSV file.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->info('Exporting all games from IGDB...');
            $filename = "games-dump-".now()->format('Ymd-U').".csv";
            $path     = storage_path('app/public/igdb/'.$filename);

            $jobs = [];
            for ($i = 0; $i < 150; $i++) {
                $jobs[] = new ExportGamesFromIGDBJob($i, $path);
            }

            $this->withProgressBar($jobs, fn($job) => Bus::dispatch($job));
            $this->newLine();
            $this->info('Finished exporting all games from IGDB.');
        } catch (Exception|Throwable $e) {
            $this->error('An error occurred while exporting all games from IGDB: '.$e->getMessage());
        }
    }
}
