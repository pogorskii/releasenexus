<?php

namespace Database\Seeders;

use App\Models\Game;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // clear ray
        ray()->clearAll();
//        $csvString = Storage::disk('public')->get('games-library.csv');
//        $lines     = explode(PHP_EOL, $csvString);
////        CHANGE separator, because in some values there are \n
//
//        $header = collect(str_getcsv(array_shift($lines)));
////        ray($header);
//        $rows = collect($lines);
//
//        DB::transaction(function () use ($rows, $header) {
//            $rows->each(function ($row) use ($header) {
////                $rowArray = collect(str_getcsv($row));
////                $rowArray = $row;
//
//                // skip if $row is empty
//                if (empty($row)) {
//                    return;
//                }
//
//                // Get the row as an array, including empty values
//                $rowArray = str_getcsv($row);
//
//                ray($row);
//                ray($rowArray);
//
//                $game = Game::create([
//                    'origin_id'               => $rowArray[0],
//                    'name'                    => $rowArray[31],
//                    'slug'                    => $rowArray[43],
//                    //                    'summary'                 => $rowArray[47] && $rowArray[47] != '"' ? $rowArray[47] : null,
//                    'aggregated_rating'       => $rowArray[2] && $rowArray[2] != '"' ? $rowArray[2] : null,
//                    'aggregated_rating_count' => $rowArray[3] && $rowArray[3] != '"' ? $rowArray[3] : null,
//                    'hypes'                   => $rowArray[26] && $rowArray[26] != '"' ? $rowArray[26] : null,
//                    'status'                  => $rowArray[45] && $rowArray[45] != '"' ? $rowArray[45] : null,
//                    //                    'version_title'           => $rowArray[55] && $rowArray[55] != '"' ? $rowArray[55] : null,
//                    'synced_at'               => now(),
//                ]);
//            });
//        });

//        HUHUHUHHUHUH

//        $rows->each(function ($row) use ($header) {
//            $rowArray = collect(str_getcsv($row));

//            $book = LibraryBook::create([
//                'code'           => $rowArray[0],
//                'title'          => [
//                    'arabic'  => $rowArray[2],
//                    'arabizi' => $rowArray[3],
//                    'english' => $rowArray[4],
//                ],
//                'author_id'      => Author::firstWhere('name', $rowArray[5])->id,
//                'illustrator_id' => Illustrator::firstWhere('name', $rowArray[6])->id,
//                'publisher_id'   => Publisher::firstWhere('name', $rowArray[7])->id,
//                'language'       => $rowArray[9],
//                'type'           => $rowArray[10],
//            ]);

//            $stages = array_map('trim', explode('&', $rowArray[8]));
//            foreach ($stages as $stage) {
//                $course = Course::where('subject_id', 1)->where('name', 'like', "Stage $stage")->first();
//                $book->courses()->attach($course->id);
//            }

//            $book->branches()->attach(1, ['quantity' => $rowArray[1] ?: 0]);

//        $csvString = Storage::disk('public')->get('games-library.csv');
//        get path to game-test.csv
        $path = storage_path('app/public/games-test.csv');

        $reader = Reader::createFromPath($path, 'r');
        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();

        DB::transaction(function () use ($records) {
            foreach ($records as $record) {
                $game = Game::create([
                    'origin_id'               => $record[0],
                    'name'                    => $record[31],
                    'slug'                    => $record[43],
                    'aggregated_rating'       => $record[2],
                    'aggregated_rating_count' => $record[3],
                    'hypes'                   => $record[26],
                    'status'                  => $record[45],
                    'synced_at'               => now(),
                ]);
            }
        });
    }
}
