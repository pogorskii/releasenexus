<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameGAgeRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'g_age_rating_id',
    ];
}
