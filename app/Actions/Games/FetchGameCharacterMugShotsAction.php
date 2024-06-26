<?php

namespace App\Actions\Games;

use App\Actions\Games\IGDB\FetchFromIGDBAction;

class FetchGameCharacterMugShotsAction
{
    public static function execute(int $offsetMultiplier, string $sortingRule = "id asc", array $fields = ['*'], int $limit = 2000, string $filter = null): array
    {
        return FetchFromIGDBAction::execute('character_mug_shots', $offsetMultiplier, $sortingRule, $fields, $limit, $filter);
    }
}
