<?php

namespace App\Console\Commands;

use App\Actions\Games\TransitGamesFromIGDBToDB;
use Exception;
use Illuminate\Console\Command;

class DumpGames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'games:dump';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetches games from IGDB and adds them to the database.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->info('Dumping games into the database...');
            $result = TransitGamesFromIGDBToDB::execute();
            $this->info("{$result['written']} games successfully added to the DB, {$result['skipped']} games skipped.");
        } catch (Exception $e) {
            $this->error('An error occurred while dumping games into the database.');
            // Log error
        }
    }
}
