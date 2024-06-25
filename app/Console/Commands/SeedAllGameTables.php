<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGamesAction;
use App\Jobs\SeedGamesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedAllGameTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-all';
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
            $this->info('Initiated master game seeder...');

            $this->call('igdb:seed-games');
            $this->call('igdb:seed-game-platforms');
            $this->call('igdb:seed-game-release-date-statuses');
            $this->call('igdb:seed-game-release-dates');
            $this->call('igdb:seed-game-covers');
            $this->call('igdb:seed-game-franchises');

            $this->newLine();
            $this->info('Finished seeding all game tables.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game tables: '.$e->getMessage());
        }
    }
}
