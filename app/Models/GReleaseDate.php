<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

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

    public function platform(): BelongsTo
    {
        return $this->belongsTo(GPlatform::class);
    }

    public function dateable(): MorphTo
    {
        return $this->morphTo('dateable', 'dateable_type', 'dateable_id', 'id');
    }
}
