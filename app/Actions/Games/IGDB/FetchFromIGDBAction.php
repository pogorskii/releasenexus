<?php

namespace App\Actions\Games\IGDB;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class FetchFromIGDBAction
{
    public static function execute(string $endpoint, int $offsetMultiplier, string $sort = 'id asc', array $fields = ['*'], int $limit = 2500): array
    {
        $fieldsString    = implode(', ', $fields);
        $limitPerRequest = $limit / 5;

        $responses = Http::pool(function (Pool $pool) use ($endpoint, $offsetMultiplier, $fieldsString, $sort, $limitPerRequest) {
            for ($i = 0; $i < 5; $i++) {
                $offsetValue = $i * $limitPerRequest + $offsetMultiplier * 5 * $limitPerRequest;
                $body        = "fields {$fieldsString}; limit {$limitPerRequest}; offset {$offsetValue}; sort {$sort};";
                $pool->as($i)->igdb()->withBody($body)->post($endpoint);
            }
        });

        $fetchedRecords = collect($responses)->map(function (Response|ConnectionException $response) {
            if ($response instanceof ConnectionException) {
                throw new ConnectionException('An error occurred while fetching records from IGDB: '.$response->getMessage());
            }

            return $response->throw()->json();
        })->toArray();

        return Arr::flatten($fetchedRecords, 1);
    }
}
