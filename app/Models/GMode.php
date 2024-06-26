<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GMode extends Model
{
    use HasFactory;

    protected $table = 'g_modes';
    protected $fillable = [
        'name',
    ];

    public function games(): BelongsToMany
    {
        return $this->belongsToMany(Game::class, 'game_g_mode');
    }
}
