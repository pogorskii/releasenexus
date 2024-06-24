<?php

namespace App\Helpers;

class GlobalHelper
{
    public static function encode_csv_json(string $key, array $array): string
    {
        return json_encode(array_key_exists($key, $array) ? $array[$key] : []);
    }
}
