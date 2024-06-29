<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GPlayerPerspective extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'created_at',
        'updated_at',
        'synced_at',
    ];

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_g_player_perspective', 'g_player_perspective_id', 'game_id')->withTimestamps();
    }
}
