<?php

namespace App\Jobs;

use App\Actions\Games\AddGameCharacterMugShotsAction;
use App\Actions\Games\AddGameCharactersAction;
use App\Actions\Games\FetchGameCharacterMugShotsAction;
use App\Actions\Games\FetchGameCharactersAction;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SeedGameCharacterMugShotsJob implements ShouldQueue
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
            Log::info('Seeding game character mug shots from IGDB chunk '.$this->chunkNumber.' started.');
            $records = FetchGameCharacterMugShotsAction::execute($this->chunkNumber, 'id asc');
            $result  = AddGameCharacterMugShotsAction::execute($records);
            Log::info('Seeding game character mug shots from IGDB chunk '.$this->chunkNumber.' result: '.json_encode($result));
        } catch (\Exception $e) {
            Log::error('An error occurred while seeding game character mug shots from IGDB chunk '.$this->chunkNumber.': '.$e->getMessage());
        }
    }

    public function middleware(): array
    {
        return [(new RateLimitedWithRedis('igdb'))];
    }
}
