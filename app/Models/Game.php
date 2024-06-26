<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_id',
        'aggregated_rating',
        'aggregated_rating_count',
        'alternative_names',
        'category',
        'checksum',
        'created_at',
        'first_release_date',
        'hypes',
        'name',
        'rating',
        'rating_count',
        'slug',
        'status',
        'storyline',
        'summary',
        'tags',
        'total_rating',
        'total_rating_count',
        'updated_at',
        'url',
        'version_title',
        'synced_at',
    ];

    public function releaseDates(): MorphMany
    {
        return $this->morphMany(GReleaseDate::class, 'dateable', 'dateable_type', 'dateable_id', 'origin_id');
    }

    public function covers(): MorphMany
    {
        return $this->morphMany(GImageable::class, 'covers', 'imageable_type', 'imageable_id', 'origin_id')->where('collection', 'covers');
    }

    public function franchises(): BelongsToMany
    {
        return $this->belongsToMany(GFranchise::class, 'game_g_franchise', 'game_id', 'g_franchise_id')->withPivot('main_franchise')->withTimestamps();
    }

    public function player_perspectives(): BelongsToMany
    {
        return $this->belongsToMany(GPlayerPerspective::class, 'game_g_player_perspective', 'game_id', 'g_player_perspective_id')->withTimestamps();
    }

    public function characters(): BelongsToMany
    {
        return $this->belongsToMany(GCharacter::class, 'g_game_character', 'game_id', 'g_character_id')->withTimestamps();
    }
}
