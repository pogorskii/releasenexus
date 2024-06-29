<?php

namespace App\Jobs\Games;

use App\Actions\Games\ConnectChildGamesAction;
use App\Actions\Games\FetchGamesAction;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConnectChildGamesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public int $chunkNumber;
    public string $path;

    /**
     * Create a new job instance.
     */
    public function __construct(int $chunkNumber)
    {
        $this->chunkNumber = $chunkNumber;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Connecting child games from IGDB chunk '.$this->chunkNumber.' started.');
            $records = FetchGamesAction::execute($this->chunkNumber, 'id asc', ['id, parent_game, version_parent, bundles, dlcs, expanded_games, expansions, forks, ports, remakes, remasters, standalone_expansions'], 2000, 'category != 0');
            $result  = ConnectChildGamesAction::execute($records);
            Log::info('Connecting child games from IGDB chunk '.$this->chunkNumber.' result: '.json_encode($result));
        } catch (\Exception $e) {
            Log::error('An error occurred while connecting child games from IGDB chunk '.$this->chunkNumber.': '.$e->getMessage());
        }
    }

    public function middleware(): array
    {
        return [(new RateLimitedWithRedis('igdb'))];
    }
}
