<?php

namespace App\Actions\Games;

use App\Actions\Games\IGDB\FetchFromIGDBAction;

class FetchGameReleaseDatesAction
{
    public static function execute(
        int $offsetMultiplier, string $sortingRule = "id asc", array $fields = ['*'], int $limit = 2000
    ): array {
        return FetchFromIGDBAction::execute('release_dates', $offsetMultiplier, $sortingRule, $fields, $limit);
    }
}
