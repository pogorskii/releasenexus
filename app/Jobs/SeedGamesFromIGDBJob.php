<?php

namespace App\Jobs;

use App\Actions\Games\AddGamesToDBAction;
use App\Actions\Games\ExportGamesToCSVAction;
use App\Actions\Games\FetchGamesAction;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SeedGamesFromIGDBJob implements ShouldQueue
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
            Log::info('Seeding games from IGDB chunk '.$this->chunkNumber.' started.');
            $games  = FetchGamesAction::execute($this->chunkNumber, 'id asc', ['*', 'alternative_names.*']);
            $result = AddGamesToDBAction::execute($games);
            Log::info('Seeding games from IGDB chunk '.$this->chunkNumber.' result: '.json_encode($result));
        } catch (Exception $e) {
            Log::error('An error occurred while seeding games from IGDB chunk '.$this->chunkNumber.': '.$e->getMessage());
        }
    }

    public function middleware(): array
    {
        return [(new RateLimitedWithRedis('igdb'))];
    }
}
