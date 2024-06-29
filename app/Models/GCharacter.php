<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class GCharacter extends Model
{
    use HasFactory;

    protected $table = 'g_characters';
    protected $fillable = [
        'id',
        'akas',
        'checksum',
        'country_name',
        'description',
        'gender',
        'name',
        'slug',
        'species',
        'url',
        'created_at',
        'updated_at',
    ];

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'g_game_character', 'g_character_id', 'game_id');
    }

    public function mug_shots(): MorphMany
    {
        return $this->morphMany(GImage::class, 'imageable')->where('collection', 'mug_shots');
    }
}
