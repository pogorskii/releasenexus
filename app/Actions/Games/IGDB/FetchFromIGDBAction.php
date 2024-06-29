<?php

namespace App\Actions\Games\IGDB;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class FetchFromIGDBAction
{
    public static function execute(string $endpoint, int $offsetMultiplier, string $sort = 'id asc', array $fields = ['*'], int $limit = 2000, string $filter = null): array
    {
        $fieldsString    = implode(', ', $fields);
        $limitPerRequest = $limit / 4;
        $filterString    = $filter ? " where {$filter};" : '';

        $responses = Http::pool(function (Pool $pool) use ($endpoint, $offsetMultiplier, $fieldsString, $sort, $filterString, $limitPerRequest) {
            for ($i = 0; $i < 4; $i++) {
                $offsetValue = $i * $limitPerRequest + $offsetMultiplier * 4 * $limitPerRequest;
                $body        = "fields {$fieldsString}; limit {$limitPerRequest}; offset {$offsetValue}; sort {$sort};$filterString";
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
