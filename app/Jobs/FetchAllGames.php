<?php

namespace App\Jobs;

use App\Actions\Games\IGDB\FetchGamesFromIGDBAction;
use App\Models\Game;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Queue\SerializesModels;

class FetchAllGames implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(): void
    {
        try {
            $games = FetchGamesFromIGDBAction::execute(5);
            foreach ($games as $game) {
                Game::create($game);
            }
        } catch (Exception $e) {
            // Log error
            throw new Exception('AnAAAAAAA error occurred while fetching games from IGDB.'.$e->getMessage());
        }
    }

    public function middleware(): array
    {
        return [new RateLimitedWithRedis('igdb')];
    }
}
