<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GLocalization extends Model
{
    use HasFactory;

    public function region(): BelongsTo
    {
        return $this->belongsTo(GRegion::class, 'g_region_id', 'id');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id', 'origin_id');
    }
}
