<?php

namespace App\Actions\Games;

use App\Helpers\GlobalHelper;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use League\Csv\Writer;

class ExportGamesToCSVAction
{
    /**
     * @throws UnavailableStream
     */
    public static function execute(array $records, string $path): array
    {
        $totalRows  = count($records);
        $wittenRows = 0;
        $failedRows = [];
        $errors     = [];

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

        $writer = Writer::createFromPath($path, 'a');
        $writer->setEndOfLine("\r\n");

        try {
            $writer->insertOne($headers);
            collect($records)->chunk(500)->each(function ($chunk) use ($writer, &$wittenRows) {
                $dataToWrite = [];
                foreach ($chunk as $record) {
                    $dataToWrite[] = [
                        $record['id'],
                        GlobalHelper::encode_csv_json('age_ratings', $record),
                        $record['aggregated_rating'] ?? '',
                        $record['aggregated_rating_count'] ?? '',
                        GlobalHelper::encode_csv_json('alternative_names', $record),
                        GlobalHelper::encode_csv_json('artworks', $record),
                        GlobalHelper::encode_csv_json('bundles', $record),
                        $record['category'] ?? '',
                        $record['checksum'] ?? '',
                        $record['collection'] ?? '',
                        GlobalHelper::encode_csv_json('collections', $record),
                        $record['cover'] ?? '',
                        $record['created_at'] ?? '',
                        GlobalHelper::encode_csv_json('dlcs', $record),
                        GlobalHelper::encode_csv_json('expanded_games', $record),
                        GlobalHelper::encode_csv_json('expansions', $record),
                        GlobalHelper::encode_csv_json('external_games', $record),
                        $record['first_release_date'] ?? '',
                        $record['follows'] ?? '',
                        GlobalHelper::encode_csv_json('forks', $record),
                        $record['franchise'] ?? '',
                        GlobalHelper::encode_csv_json('franchises', $record),
                        GlobalHelper::encode_csv_json('game_engines', $record),
                        GlobalHelper::encode_csv_json('game_localizations', $record),
                        GlobalHelper::encode_csv_json('game_modes', $record),
                        GlobalHelper::encode_csv_json('genres', $record),
                        $record['hypes'] ?? '',
                        GlobalHelper::encode_csv_json('involved_companies', $record),
                        GlobalHelper::encode_csv_json('keywords', $record),
                        GlobalHelper::encode_csv_json('language_supports', $record),
                        GlobalHelper::encode_csv_json('multiplayer_modes', $record),
                        $record['name'] ?? '',
                        $record['parent_game'] ?? '',
                        GlobalHelper::encode_csv_json('platforms', $record),
                        GlobalHelper::encode_csv_json('player_perspectives', $record),
                        GlobalHelper::encode_csv_json('ports', $record),
                        $record['rating'] ?? '',
                        $record['rating_count'] ?? '',
                        GlobalHelper::encode_csv_json('release_dates', $record),
                        GlobalHelper::encode_csv_json('remakes', $record),
                        GlobalHelper::encode_csv_json('remasters', $record),
                        GlobalHelper::encode_csv_json('screenshots', $record),
                        GlobalHelper::encode_csv_json('similar_games', $record),
                        $record['slug'] ?? '',
                        GlobalHelper::encode_csv_json('standalone_expansions', $record),
                        $record['status'] ?? '',
                        $record['storyline'] ?? '',
                        $record['summary'] ?? '',
                        GlobalHelper::encode_csv_json('tags', $record),
                        GlobalHelper::encode_csv_json('themes', $record),
                        $record['total_rating'] ?? '',
                        $record['total_rating_count'] ?? '',
                        $record['updated_at'] ?? '',
                        $record['url'] ?? '',
                        $record['version_parent'] ?? '',
                        $record['version_title'] ?? '',
                        GlobalHelper::encode_csv_json('videos', $record),
                        GlobalHelper::encode_csv_json('websites', $record),
                    ];
                }

                $writer->insertAll($dataToWrite);
                $wittenRows += count($dataToWrite);
            });
        } catch (CannotInsertRecord $e) {
            $failedRows[] = $wittenRows;
            $errors[]     = $e->getMessage();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        return [
            'totalRows'  => $totalRows,
            'wittenRows' => $wittenRows,
            'failedRows' => $failedRows,
            'errors'     => $errors,
        ];
    }
}
