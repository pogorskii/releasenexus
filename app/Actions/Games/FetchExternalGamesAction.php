<?php

namespace App\Actions\Games;

use App\Actions\Games\IGDB\FetchFromIGDBAction;

class FetchExternalGamesAction
{
    public static function execute(int $offsetMultiplier, string $sortingRule = "id asc", array $fields = ['*'], int $limit = 2000): array
    {
        return FetchFromIGDBAction::execute('external_games', $offsetMultiplier, $sortingRule, $fields, $limit);
    }
}
