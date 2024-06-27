<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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

    public function platforms(): HasManyThrough
    {
        return $this->hasManyThrough(GPlatform::class, GReleaseDate::class, 'dateable_id', 'origin_id', 'origin_id', 'platform_id');
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

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(GCollection::class, 'game_g_collection', 'game_id', 'g_collection_id')->withPivot('main_collection', 'type')->withTimestamps();
    }

    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(GGenre::class, 'g_genres_games', 'game_id', 'g_genre_id')->withTimestamps();
    }

    public function keywords(): BelongsToMany
    {
        return $this->belongsToMany(GKeyword::class, 'g_game_keyword', 'game_id', 'g_keyword_id')->withTimestamps();
    }

    public function modes(): BelongsToMany
    {
        return $this->belongsToMany(GMode::class, 'game_g_mode', 'game_id', 'g_mode_id')->withTimestamps();
    }

    public function themes(): BelongsToMany
    {
        return $this->belongsToMany(GTheme::class, 'game_g_theme', 'game_id', 'g_theme_id')->withTimestamps();
    }

    public function age_ratings(): BelongsToMany
    {
        return $this->belongsToMany(GAgeRating::class, 'game_g_age_rating', 'game_id', 'g_age_rating_id')->withTimestamps();
    }

    public function external_games(): HasMany
    {
        return $this->hasMany(GExternalGame::class, 'game_id', 'origin_id');
    }
}
