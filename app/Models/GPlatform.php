<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GPlatform extends Model
{
    use HasFactory;

    public function game_release_dates(): HasMany
    {
        return $this->hasMany(GReleaseDate::class);
    }
}
