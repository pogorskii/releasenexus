<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameGPlayerPerspective extends Model
{
    use HasFactory;

    protected $table = 'game_g_player_perspective';
    protected $fillable = [
        'game_id',
        'g_player_perspective_id',
        'updated_at',
    ];
}
