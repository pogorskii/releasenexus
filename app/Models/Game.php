<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
