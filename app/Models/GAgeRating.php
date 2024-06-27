<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GAgeRating extends Model
{
    use HasFactory;

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_g_age_rating', 'g_age_rating_id', 'game_id')->withTimestamps();
    }
}
