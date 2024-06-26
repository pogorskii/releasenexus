<?php

namespace App\Actions\Games;

use App\Actions\Games\IGDB\FetchFromIGDBAction;

class FetchGameModesAction
{
    public static function execute(
        int $offsetMultiplier, string $sortingRule = "id asc", array $fields = ['*',], int $limit = 2000
    ): array {
        return FetchFromIGDBAction::execute('game_modes', $offsetMultiplier, $sortingRule, $fields, $limit);
    }
}
