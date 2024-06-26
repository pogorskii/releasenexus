<?php

namespace App\Jobs;

use App\Actions\Games\ConnectGameModesAction;
use App\Actions\Games\FetchGamesAction;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConnectGameModesJob implements ShouldQueue
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
            Log::info('Connecting game modes from IGDB chunk '.$this->chunkNumber.' started.');
            $records = FetchGamesAction::execute($this->chunkNumber, 'id asc', ['id, game_modes'], 2000, 'game_modes != null');
            $result  = ConnectGameModesAction::execute($records);
            Log::info('Connecting game modes from IGDB chunk '.$this->chunkNumber.' result: '.json_encode($result));
        } catch (\Exception $e) {
            Log::error('An error occurred while connecting game modes from IGDB chunk '.$this->chunkNumber.': '.$e->getMessage());
        }
    }

    public function middleware(): array
    {
        return [(new RateLimitedWithRedis('igdb'))];
    }
}
