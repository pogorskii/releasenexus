<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GReleaseDate extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_id',
        'category',
        'checksum',
        'created_at',
        'date',
        'human',
        'm',
        'region',
        'status_id',
        'updated_at',
        'y',
        'dateable_id',
        'dateable_type',
    ];

    public function dateable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo('dateable', 'dateable_type', 'dateable_id', 'origin_id');
    }
}
