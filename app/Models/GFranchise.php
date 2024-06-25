<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GFranchise extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_id',
        'name',
        'slug',
        'updated_at',
    ];

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_g_franchise', 'g_franchise_id', 'game_id')->withPivot('main_franchise')->withTimestamps();
    }
}
