<?php

namespace App\Console\Commands;

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
    protected $description = 'Fetches games from IGDB and dumps them into the database.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        try {
            $this->info('Dumping games into the database...');
            $this->withProgressBar(2,\App\Actions\IGDB\TransitGamesFromOriginToDB::execute());
//           $this->withProgressBar(\App\Actions\IGDB\TransitGamesFromOriginToDB::execute());
            $this->info('/n Games dumped successfully.');
        } catch (\Exception $e) {
            $this->error('An error occurred while dumping games into the database.');
            // Log error
        }
    }
}
