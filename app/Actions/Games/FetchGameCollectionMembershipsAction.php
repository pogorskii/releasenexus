<?php

namespace App\Actions\Games;

use App\Actions\Games\IGDB\FetchFromIGDBAction;

class FetchGameCollectionMembershipsAction
{
    public static function execute(int $offsetMultiplier, string $sortingRule = "id asc", array $fields = ['*'], int $limit = 2000, string $filter = null): array
    {
        return FetchFromIGDBAction::execute('collection_memberships', $offsetMultiplier, $sortingRule, $fields, $limit, $filter);
    }
}
