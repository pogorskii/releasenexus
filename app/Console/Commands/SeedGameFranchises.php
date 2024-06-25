<?php

namespace App\Console\Commands;

use App\Actions\Games\FetchGameFranchisesAction;
use App\Jobs\SeedGameFranchisesJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;

class SeedGameFranchises extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'igdb:seed-game-franchises';
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
            $this->info('Seeding all game franchises from IGDB...');

            $chunkNumber = 0;
            do {
                $job = new SeedGameFranchisesJob($chunkNumber);
                Bus::dispatch($job);
                $chunkNumber++;
            } while (count(FetchGameFranchisesAction::execute($chunkNumber, 'id asc', ['id'])) > 0);

            $this->newLine();
            $this->info('Finished seeding all game franchises from IGDB.');
        } catch (\Exception|\Throwable $e) {
            $this->error('An error occurred while seeding all game franchises from IGDB: '.$e->getMessage());
        }
    }
}
