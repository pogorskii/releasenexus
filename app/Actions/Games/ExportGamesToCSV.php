<?php

namespace App\Actions\Games;

use App\Models\Game;

class ExportGamesToCSV
{
    public static function execute(array $games): \Symfony\Component\HttpFoundation\StreamedResponse
    {
//        $csv = fopen('games.csv', 'w');
//        fputcsv($csv, array_keys($games[0]));
//
//        foreach ($games as $game) {
//            fputcsv($csv, $game);
//        }
//
//        fclose($csv);

        $filename = 'games-test.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($games) {
            $handle = fopen('php://output', 'w');

            // Add CSV headers
//            fputcsv($handle, [
//                'IGDB ID',
//                'Name',
//                'Release Date'
//
//            ]);

            fputcsv($handle, \Schema::getColumnListing('games'));

            // Fetch and process data in chunks
//            Game::chunk(500, function ($games) use ($handle) {
//                foreach ($games as $game) {
//                    // Extract data from each game.
//                    $data = [
//                        $game->origin_id,
//                        $game->name,
//                        $game->original_release_date ?? '',
//                        //                        isset($game->skills)? implode(", ", json_decode($game->skills)) : '',
//                    ];
//
//                    // Write data to a CSV file.
//                    fputcsv($handle, $data);
//                }
//            });

            collect($games)->chunk(500)->each(function ($chunk) use ($handle) {
                foreach ($chunk as $game) {
                    // Extract data from each game.
                    $data = [
                        $game['id'],
                        $game['name'],
                        $game['release_date'] ?? '',
                        //                        isset($game->skills)? implode(", ", json_decode($game->skills)) : '',
                    ];

                    // Write data to a CSV file.
                    fputcsv($handle, $data);
                }
            });

            // Close CSV file handle
            fclose($handle);
        }, 200, $headers);
    }
}
