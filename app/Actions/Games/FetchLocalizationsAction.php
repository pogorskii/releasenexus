<?php

namespace App\Actions\Games;

use App\Actions\Games\IGDB\FetchFromIGDBAction;

class FetchLocalizationsAction
{
    public static function execute(
        int $offsetMultiplier, string $sortingRule = "id asc", array $fields = ['*',], int $limit = 2000
    ): array {
        return FetchFromIGDBAction::execute('game_localizations', $offsetMultiplier, $sortingRule, $fields, $limit);
    }
}
