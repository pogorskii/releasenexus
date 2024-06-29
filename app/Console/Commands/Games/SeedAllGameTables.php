<?php

namespace App\Console\Commands\Games;

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

//            $this->call('igdb:seed-games');
            $this->call('igdb:seed-platforms');
            $this->call('igdb:seed-release-date-statuses');
            $this->call('igdb:seed-release-dates');
            $this->call('igdb:seed-covers');
            $this->call('igdb:seed-franchises');
            $this->call('igdb:connect-franchises');
            $this->call('igdb:seed-characters');
            $this->call('igdb:seed-player-perspectives');
            $this->call('igdb:connect-player-perspectives');
            $this->call('igdb:seed-character-mug-shots');
            $this->call('igdb:connect-character-mug-shots');
            $this->call('igdb:seed-collections');
            $this->call('igdb:connect-collections-from-memberships');
            $this->call('igdb:connect-collections-from-games');
            $this->call('igdb:seed-keywords');
            $this->call('igdb:connect-keywords');
            $this->call('igdb:seed-genres');
            $this->call('igdb:connect-genres');
            $this->call('igdb:seed-modes');
            $this->call('igdb:connect-modes');
            $this->call('igdb:seed-themes');
            $this->call('igdb:connect-themes');
//            $this->call('igdb:seed-age-ratings');
            $this->call('igdb:connect-age-ratings');
            $this->call('igdb:seed-external-games');
            $this->call('igdb:seed-multiplayer-modes');
            $this->call('igdb:seed-languages');
            $this->call('igdb:seed-language-supports');
            $this->call('igdb:seed-regions');
            $this->call('igdb:seed-localizations');
            $this->call('igdb:seed-company-logos');
            $this->call('igdb:seed-company-websites');
            $this->call('igdb:seed-companies');
            $this->call('igdb:connect-companies');
            $this->call('igdb:connect-companies-from-involved-companies');
            $this->call('igdb:seed-engine-logos');
            $this->call('igdb:seed-engines');
            $this->call('igdb:seed-platform-logos');
            $this->call('igdb:seed-platform-versions');
            $this->call('igdb:connect-platform-versions');
            $this->call('igdb:seed-platform-version-release-dates');
            $this->call('igdb:connect-platform-version-release-dates');
            $this->call('igdb:seed-network-types');
            $this->call('igdb:seed-videos');
            $this->call('igdb:seed-events');
            $this->call('igdb:seed-event-networks');
            $this->call('igdb:seed-event-logos');
            $this->call('igdb:seed-game-websites');
            $this->call('igdb:seed-screenshots');
//            $this->call('igdb:connect-child-games');

            $this->newLine();
            $this->info('Finished seeding all game tables.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game tables: '.$e->getMessage());
        }
    }
}
