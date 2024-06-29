<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GCollection extends Model
{
    use HasFactory;

    protected $table = 'g_collections';
    protected $fillable = [
        'name',
        'slug',
        'url',
        'updated_at',
    ];

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_g_collection', 'g_collection_id', 'game_id')->withPivot('main_collection', 'type')->withTimestamps();
    }
}
