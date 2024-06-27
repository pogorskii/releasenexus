<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GLanguage extends Model
{
    use HasFactory;

    protected $table = 'g_languages';

    public function language_supports(): HasMany
    {
        return $this->hasMany(GLanguageSupport::class, 'g_language_id', 'id');
    }
}
