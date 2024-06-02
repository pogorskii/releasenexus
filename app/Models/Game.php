<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_id',
        'name',
        'slug',
        'summary',
        'first_release_date',
        'aggregated_rating',
        'aggregated_rating_count',
        'hypes',
        'status',
        'version_title',
        'synced_at',
    ];
}
