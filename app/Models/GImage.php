<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class GImage extends Model
{
    use HasFactory;

    public function cover_of_game(): MorphToMany
    {
        return $this->morphedByMany(Game::class, 'imageable', 'imageable_type', 'imageable_id', 'image_id')->where('collection', 'covers');
    }
}
