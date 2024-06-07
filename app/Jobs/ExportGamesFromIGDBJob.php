<?php

namespace App\Jobs;

use App\Actions\Games\ExportGamesToCSV;
use App\Actions\Games\IGDB\FetchGamesFromIGDBAction;
use Exception;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
     * @throws Exception
     */
    public function handle(): void
    {
        try {
            $games = FetchGamesFromIGDBAction::execute($this->chunkNumber, 'id asc');
            ExportGamesToCSV::execute($games, $this->path);
        } catch (Exception $e) {
            // Log error
            throw new Exception('An error occurred while fetching games from IGDB.'.$e->getMessage());
        }
    }
}
