<?php

namespace App\Actions\Games;

use Illuminate\Support\Facades\Storage;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use League\Csv\Writer;

function encode_json_or_save_empty_value(string $key, array $array): string
{
    return json_encode(array_key_exists($key, $array) ? $array[$key] : []);
}

class ExportGamesToCSV
{
//           $encode_json_or_save_empty_string = fn(string $key, array $array) => json_encode(array_key_exists($key, $array) ? $array[$key] : []);
    /**
     * @throws UnavailableStream
     */
    public static function execute(array $games, string $path): void
    {
//        $handle = fopen($path, 'a');
        $writer = Writer::createFromPath($path, 'a');

//        fputcsv($handle, [
//            'id',
//            'age_ratings',
//            'aggregated_rating',
//            'aggregated_rating_count',
//            'alternative_names',
//            'artworks',
//            'bundles',
//            'category',
//            'checksum',
//            'collection',
//            'collections',
//            'cover',
//            'created_at',
//            'dlcs',
//            'expanded_games',
//            'expansions',
//            'external_games',
//            'first_release_date',
//            'follows',
//            'forks',
//            'franchise',
//            'franchises',
//            'game_engines',
//            'game_localizations',
//            'game_modes',
//            'genres',
//            'hypes',
//            'involved_companies',
//            'keywords',
//            'language_supports',
//            'multiplayer_modes',
//            'name',
//            'parent_game',
//            'platforms',
//            'player_perspectives',
//            'ports',
//            'rating',
//            'rating_count',
//            'release_dates',
//            'remakes',
//            'remasters',
//            'screenshots',
//            'similar_games',
//            'slug',
//            'standalone_expansions',
//            'status',
//            'storyline',
//            'summary',
//            'tags',
//            'themes',
//            'total_rating',
//            'total_rating_count',
//            'updated_at',
//            'url',
//            'version_parent',
//            'version_title',
//            'videos',
//            'websites',
//        ]);

        $headers = [
            'id',
            'age_ratings',
            'aggregated_rating',
            'aggregated_rating_count',
            'alternative_names',
            'artworks',
            'bundles',
            'category',
            'checksum',
            'collection',
            'collections',
            'cover',
            'created_at',
            'dlcs',
            'expanded_games',
            'expansions',
            'external_games',
            'first_release_date',
            'follows',
            'forks',
            'franchise',
            'franchises',
            'game_engines',
            'game_localizations',
            'game_modes',
            'genres',
            'hypes',
            'involved_companies',
            'keywords',
            'language_supports',
            'multiplayer_modes',
            'name',
            'parent_game',
            'platforms',
            'player_perspectives',
            'ports',
            'rating',
            'rating_count',
            'release_dates',
            'remakes',
            'remasters',
            'screenshots',
            'similar_games',
            'slug',
            'standalone_expansions',
            'status',
            'storyline',
            'summary',
            'tags',
            'themes',
            'total_rating',
            'total_rating_count',
            'updated_at',
            'url',
            'version_parent',
            'version_title',
            'videos',
            'websites',
        ];

        $writer->insertOne($headers);
        $writer->setEscape('');
        $writer->setEndOfLine("\r\n");

        collect($games)->chunk(500)->each(function ($chunk) use ($writer) {
            $dataToWrite = [];
            foreach ($chunk as $game) {
                if ($game['id'] != 1) {
                    continue;
                }
                $dataToWrite[] = [
                    $game['id'],
                    json_encode($game['age_ratings'] ?? ''),
                    $game['aggregated_rating'] ?? '',
                    $game['aggregated_rating_count'] ?? '',
                    encode_json_or_save_empty_value('alternative_names', $game),
                    encode_json_or_save_empty_value('artworks', $game),
                    encode_json_or_save_empty_value('bundles', $game),
                    $game['category'] ?? '',
                    $game['checksum'] ?? '',
                    $game['collection'] ?? '',
                    encode_json_or_save_empty_value('collections', $game),
                    $game['cover'] ?? '',
                    $game['created_at'] ?? '',
                    encode_json_or_save_empty_value('dlcs', $game),
                    encode_json_or_save_empty_value('expanded_games', $game),
                    encode_json_or_save_empty_value('expansions', $game),
                    encode_json_or_save_empty_value('external_games', $game),
                    $game['first_release_date'] ?? '',
                    $game['follows'] ?? '',
                    encode_json_or_save_empty_value('forks', $game),
                    $game['franchise'] ?? '',
                    encode_json_or_save_empty_value('franchises', $game),
                    encode_json_or_save_empty_value('game_engines', $game),
                    encode_json_or_save_empty_value('game_localizations', $game),
                    encode_json_or_save_empty_value('game_modes', $game),
                    encode_json_or_save_empty_value('genres', $game),
                    $game['hypes'] ?? '',
                    encode_json_or_save_empty_value('involved_companies', $game),
                    encode_json_or_save_empty_value('keywords', $game),
                    encode_json_or_save_empty_value('language_supports', $game),
                    encode_json_or_save_empty_value('multiplayer_modes', $game),
                    $game['name'] ?? '',
                    $game['parent_game'] ?? '',
                    encode_json_or_save_empty_value('platforms', $game),
                    encode_json_or_save_empty_value('player_perspectives', $game),
                    encode_json_or_save_empty_value('ports', $game),
                    $game['rating'] ?? '',
                    $game['rating_count'] ?? '',
                    encode_json_or_save_empty_value('release_dates', $game),
                    encode_json_or_save_empty_value('remakes', $game),
                    encode_json_or_save_empty_value('remasters', $game),
                    encode_json_or_save_empty_value('screenshots', $game),
                    encode_json_or_save_empty_value('similar_games', $game),
                    $game['slug'] ?? '',
                    encode_json_or_save_empty_value('standalone_expansions', $game),
                    $game['status'] ?? '',
                    $game['storyline'] ? "_{$game['storyline']}_" : '',
                    $game['summary'] ?? '',
                    encode_json_or_save_empty_value('tags', $game),
                    encode_json_or_save_empty_value('themes', $game),
                    $game['total_rating'] ?? '',
                    $game['total_rating_count'] ?? '',
                    $game['updated_at'] ?? '',
                    $game['url'] ?? '',
                    $game['version_parent'] ?? '',
                    $game['version_title'] ?? '',
                    encode_json_or_save_empty_value('videos', $game),
                    encode_json_or_save_empty_value('websites', $game),
                ];
//                fputcsv($handle, $data);
            }

            $writer->insertAll($dataToWrite);
        });
//        fclose($handle);
    }
}
