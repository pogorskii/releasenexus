<?php

namespace App\Actions\Games;

use App\Actions\Games\IGDB\FetchFromIGDBAction;

class FetchFranchisesAction
{
    public static function execute(
        int $offsetMultiplier, string $sortingRule = "id asc", array $fields = ['*'], int $limit = 2000
    ): array {
        return FetchFromIGDBAction::execute('franchises', $offsetMultiplier, $sortingRule, $fields, $limit);
    }
}
