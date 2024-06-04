<?php

namespace App\Console\Commands;

use App\Jobs\FetchAllGames;
use Exception;
use Illuminate\Console\Command;

class TestJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:job';
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
            FetchAllGames::dispatch();
            $this->info('Job dispatched.');
        } catch (Exception $e) {
            $this->error('An error occurred while testing the job.'.$e->getMessage());
        }
    }
}
