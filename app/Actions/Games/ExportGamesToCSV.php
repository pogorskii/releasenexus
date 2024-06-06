<?php

namespace App\Actions\Games;

use App\Models\Game;
use Illuminate\Support\Facades\Storage;

class ExportGamesToCSV
{
    public static function execute(array $games): void
    {
        // Write all games in a csv file and save it to public disk.
        $filename = 'games-test.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $handle = fopen($filename, 'w');

        // Add CSV headers
        fputcsv($handle, [
            'IGDB ID',
            'Name',
            'Release Date'
        ]);

        collect($games)->chunk(500)->each(function ($chunk) use ($handle) {
            foreach ($chunk as $game) {
                // Extract data from each game.
                $data = [
                    $game['id'],
                    $game['name'],
                    $game['release_date'] ?? '',
                ];

                // Write data to a CSV file.
                fputcsv($handle, $data);
            }
        });

        // Close CSV file handle
        fclose($handle);


        // Get the current date and time and convert it to a string.
        $currentDate = now()->toDateString();

        // Replace : with - in the time string.
$currentTime = str_replace(':', '-', now()->toTimeString());

        // Save the CSV file to the storage disk, but first convert handle from resource to string.
        Storage::disk('public')->put("{$filename}-{$currentDate}-{$currentTime}", file_get_contents($filename));

//        $filename = 'games-test.csv';
//
//        $headers = [
//            'Content-Type' => 'text/csv',
//            'Content-Disposition' => "attachment; filename=\"$filename\"",
//            'Pragma' => 'no-cache',
//            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
//            'Expires' => '0',
//        ];
//
//        return response()->stream(function () use ($games, $filename) {
//            $handle = fopen('php://output', 'w');
//
//            // Add CSV headers
//            fputcsv($handle, [
//                'IGDB ID',
//                'Name',
//                'Release Date'
//
//            ]);
//
//            collect($games)->chunk(500)->each(function ($chunk) use ($handle) {
//                foreach ($chunk as $game) {
//                    // Extract data from each game.
//                    $data = [
//                        $game['id'],
//                        $game['name'],
//                        $game['release_date'] ?? '',
//                    ];
//
//                    // Write data to a CSV file.
//                    fputcsv($handle, $data);
//                }
//            });
//
//            // Close CSV file handle
//            fclose($handle);
//
//            // Save the CSV file to the storage disk.
//            Storage::disk('public')->put($filename, $handle);
//        }, 200, $headers);
    }
}
