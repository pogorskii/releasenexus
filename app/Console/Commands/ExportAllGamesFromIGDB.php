<?php

namespace App\Console\Commands;

use App\Jobs\ExportGamesFromIGDBJob;
use Exception;
use Illuminate\Bus\Batch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Log;
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
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->info('Testing job...');
            $filename = "games-test-".now()->format('Ymd-U').".csv";
            $path     = storage_path('app/public/'.$filename);

            $jobs = [];
            for ($i = 0; $i < 1; $i++) {
                $jobs[] = new ExportGamesFromIGDBJob($i, $path);
            }

            Bus::batch($jobs)->before(function (Batch $batch) {
                Log::info('Exporting games from IGDB started.');
            })->progress(function (Batch $batch) {
                Log::info('Exporting games from IGDB progress: '.$batch->progress().'%');
            })->catch(function (Batch $batch, Throwable $e) {
                Log::error('Error exporting games from IGDB: '.$e->getMessage());
            })->finally(function (Batch $batch) {
                Log::info('Exporting games from IGDB completed.');
            })->dispatch();
            $this->info('Job finished.');
        } catch (Exception|Throwable $e) {
            $this->error('An error occurred while testing the job.'.$e->getMessage());
        }
    }
}
