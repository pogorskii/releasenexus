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
    public static function execute(array $games, string $path): array
    {
        $totalRows  = count($games);
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
            collect($games)->chunk(500)->each(function ($chunk) use ($writer, &$wittenRows) {
                $dataToWrite = [];
                foreach ($chunk as $game) {
                    $dataToWrite[] = [
                        $game['id'],
                        GlobalHelper::encode_csv_json('age_ratings', $game),
                        $game['aggregated_rating'] ?? '',
                        $game['aggregated_rating_count'] ?? '',
                        GlobalHelper::encode_csv_json('alternative_names', $game),
                        GlobalHelper::encode_csv_json('artworks', $game),
                        GlobalHelper::encode_csv_json('bundles', $game),
                        $game['category'] ?? '',
                        $game['checksum'] ?? '',
                        $game['collection'] ?? '',
                        GlobalHelper::encode_csv_json('collections', $game),
                        $game['cover'] ?? '',
                        $game['created_at'] ?? '',
                        GlobalHelper::encode_csv_json('dlcs', $game),
                        GlobalHelper::encode_csv_json('expanded_games', $game),
                        GlobalHelper::encode_csv_json('expansions', $game),
                        GlobalHelper::encode_csv_json('external_games', $game),
                        $game['first_release_date'] ?? '',
                        $game['follows'] ?? '',
                        GlobalHelper::encode_csv_json('forks', $game),
                        $game['franchise'] ?? '',
                        GlobalHelper::encode_csv_json('franchises', $game),
                        GlobalHelper::encode_csv_json('game_engines', $game),
                        GlobalHelper::encode_csv_json('game_localizations', $game),
                        GlobalHelper::encode_csv_json('game_modes', $game),
                        GlobalHelper::encode_csv_json('genres', $game),
                        $game['hypes'] ?? '',
                        GlobalHelper::encode_csv_json('involved_companies', $game),
                        GlobalHelper::encode_csv_json('keywords', $game),
                        GlobalHelper::encode_csv_json('language_supports', $game),
                        GlobalHelper::encode_csv_json('multiplayer_modes', $game),
                        $game['name'] ?? '',
                        $game['parent_game'] ?? '',
                        GlobalHelper::encode_csv_json('platforms', $game),
                        GlobalHelper::encode_csv_json('player_perspectives', $game),
                        GlobalHelper::encode_csv_json('ports', $game),
                        $game['rating'] ?? '',
                        $game['rating_count'] ?? '',
                        GlobalHelper::encode_csv_json('release_dates', $game),
                        GlobalHelper::encode_csv_json('remakes', $game),
                        GlobalHelper::encode_csv_json('remasters', $game),
                        GlobalHelper::encode_csv_json('screenshots', $game),
                        GlobalHelper::encode_csv_json('similar_games', $game),
                        $game['slug'] ?? '',
                        GlobalHelper::encode_csv_json('standalone_expansions', $game),
                        $game['status'] ?? '',
                        $game['storyline'] ?? '',
                        $game['summary'] ?? '',
                        GlobalHelper::encode_csv_json('tags', $game),
                        GlobalHelper::encode_csv_json('themes', $game),
                        $game['total_rating'] ?? '',
                        $game['total_rating_count'] ?? '',
                        $game['updated_at'] ?? '',
                        $game['url'] ?? '',
                        $game['version_parent'] ?? '',
                        $game['version_title'] ?? '',
                        GlobalHelper::encode_csv_json('videos', $game),
                        GlobalHelper::encode_csv_json('websites', $game),
                    ];
                }

                $writer->insertAll($dataToWrite);
                $wittenRows += count($dataToWrite);
            });
        } catch (CannotInsertRecord $e) {
            $failedRows[] = $wittenRows;
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
