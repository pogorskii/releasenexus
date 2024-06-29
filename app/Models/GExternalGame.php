<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GExternalGame extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_id',
        'category',
        'created_at',
        'updated_at',
        'synced_at',
    ];

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(GPlatform::class, 'g_external_game_g_platforms', 'g_external_game_id', 'g_platform_id')->withTimestamps();
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id');
    }
}
