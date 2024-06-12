<?php

namespace App\Actions\Games;

use App\Actions\Games\IGDB\FetchFromIGDBAction;

class FetchAgeRatingsAction
{
    public static function execute(int $offsetMultiplier, string $sortingRule = "id asc", array $fields = ['*'], int $limit = 2500): array
    {
        return FetchFromIGDBAction::execute('age_ratings', $offsetMultiplier, $sortingRule, $fields, $limit);
    }
}
