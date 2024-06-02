<?php

namespace App\Console\Commands;

use App\Actions\Games\AddGamesToDBAction;
use Exception;
use Illuminate\Console\Command;

class StoreGamesInDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:store-games';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(array $games): void
    {
        try {
            $this->info('Dumping games into the database...');
            $result = AddGamesToDBAction::execute($games);
            $this->info("{$result['written']} games successfully added to the DB, {$result['skipped']} games skipped.");
        } catch (Exception $e) {
            $this->error('An error occurred while dumping games into the database.');
            // Log error
        }
    }
}
