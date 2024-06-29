<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GRegion extends Model
{
    use HasFactory;

    public function localizations(): HasMany
    {
        return $this->hasMany(GLocalization::class, 'g_region_id', 'id');
    }
}
