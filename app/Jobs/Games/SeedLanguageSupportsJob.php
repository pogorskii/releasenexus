<?php

namespace App\Jobs\Games;

use App\Actions\Games\AddLanguageSupportsAction;
use App\Actions\Games\FetchLanguageSupportsAction;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SeedLanguageSupportsJob implements ShouldQueue
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
            Log::info('Seeding game language supports from IGDB chunk '.$this->chunkNumber.' started.');
            $records = FetchLanguageSupportsAction::execute($this->chunkNumber, 'id asc');
            $result  = AddLanguageSupportsAction::execute($records);
            Log::info('Seeding game language supports from IGDB chunk '.$this->chunkNumber.' result: '.json_encode($result));
        } catch (\Exception $e) {
            Log::error('An error occurred while seeding game language supports from IGDB chunk '.$this->chunkNumber.': '.$e->getMessage());
        }
    }

    public function middleware(): array
    {
        return [(new RateLimitedWithRedis('igdb'))];
    }
}
