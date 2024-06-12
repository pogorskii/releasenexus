<?php

namespace App\Jobs;

use App\Actions\Games\ExportGamesToCSVAction;
use App\Actions\Games\IGDB\FetchGamesFromIGDBAction;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExportGamesFromIGDBJob implements ShouldQueue
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
        Log::info('Exporting games from IGDB chunk '.$this->chunkNumber.' started.');
        $games  = FetchGamesFromIGDBAction::execute($this->chunkNumber, 'id asc');
        $result = ExportGamesToCSVAction::execute($games, $this->path);
        Log::info('Exporting games from IGDB chunk '.$this->chunkNumber.' result: '.json_encode($result));
    }
}
