<?php

namespace App\Jobs\Games;

use App\Actions\Games\AddLocalizationsAction;
use App\Actions\Games\FetchLocalizationsAction;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SeedLocalizationsJob implements ShouldQueue
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
            Log::info('Seeding game localizations from IGDB chunk '.$this->chunkNumber.' started.');
            $records = FetchLocalizationsAction::execute($this->chunkNumber, 'id asc', ['*']);
            $result  = AddLocalizationsAction::execute($records);
            Log::info('Seeding game localizations from IGDB chunk '.$this->chunkNumber.' result: '.json_encode($result));
        } catch (\Exception $e) {
            Log::error('An error occurred while seeding game localizations from IGDB chunk '.$this->chunkNumber.': '.$e->getMessage());
        }
    }

    public function middleware(): array
    {
        return [(new RateLimitedWithRedis('igdb'))];
    }
}
