<?php

namespace App\Jobs;

use App\Actions\Games\ExportAgeRatingsToCSVAction;
use App\Actions\Games\FetchAgeRatingsAction;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExportAgeRatingsFromIGDBJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;

    public int $chunkNumber;
    public string $path;

    /**
     * Create a new job instance.
     */
    public function __construct(int $chunkNumber, string $path)
    {
        $this->chunkNumber = $chunkNumber;
        $this->path        = $path;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('Exporting age ratings from IGDB chunk '.$this->chunkNumber.' started.');
            $ageRatings = FetchAgeRatingsAction::execute($this->chunkNumber);
            $result     = ExportAgeRatingsToCSVAction::execute($ageRatings, $this->path);
            Log::info('Exporting age ratings from IGDB chunk '.$this->chunkNumber.' result: '.json_encode($result));
        } catch (Exception $e) {
            Log::error('An error occurred while exporting age ratings from IGDB chunk '.$this->chunkNumber.': '.$e->getMessage());
        }
    }

    public function middleware(): array
    {
        return [(new RateLimitedWithRedis('igdb'))];
    }
}
