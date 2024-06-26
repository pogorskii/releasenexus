<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
            $this->call('igdb:connect-game-franchises');
            $this->call('igdb:seed-game-characters');
            $this->call('igdb:seed-game-player-perspectives');
            $this->call('igdb:connect-game-player-perspectives');
            $this->call('igdb:seed-game-character-mug-shots');
            $this->call('igdb:connect-game-character-mug-shots');
            $this->call('igdb:seed-game-collections');
            $this->call('igdb:connect-game-collections-from-memberships');
            $this->call('igdb:connect-game-collections-from-games');
            $this->call('igdb:seed-game-keywords');
            $this->call('igdb:connect-game-keywords');

            $this->newLine();
            $this->info('Finished seeding all game tables.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game tables: '.$e->getMessage());
        }
    }
}
