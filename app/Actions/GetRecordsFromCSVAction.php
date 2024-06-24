<?php

namespace App\Actions;

use Exception;
use League\Csv\Reader;

class GetRecordsFromCSVAction
{
    public static function execute(string $pathToFile): array
    {
        $records = [];
        $errors  = [];

        $path      = storage_path($pathToFile);
        $formatter = fn(array $row): array => array_map(fn($value) => $value == '' ? null : $value, $row);
        try {
            $reader = Reader::createFromPath($path, 'r');
            $reader->setHeaderOffset(0)->addFormatter($formatter);

            $records = $reader->getRecords();
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        return [
            'records' => $records,
            'errors'  => $errors,
        ];
    }
}
