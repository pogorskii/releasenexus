<?php

namespace App\Actions\Games;

use App\Helpers\GlobalHelper;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\UnavailableStream;
use League\Csv\Writer;

class ExportAgeRatingsToCSVAction
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
            'category',
            'checksum',
            'content_descriptions',
            'rating',
            'rating_cover_url',
            'synopsis',
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
                        $record['category'] ?? '',
                        $record['checksum'],
                        GlobalHelper::encode_csv_json('content_descriptions', $record),
                        $record['rating'] ?? '',
                        $record['rating_cover_url'] ?? '',
                        $record['synopsis'] ?? '',
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
