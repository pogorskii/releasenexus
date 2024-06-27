<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GLanguageSupport extends Model
{
    use HasFactory;

    protected $table = 'g_language_supports';
    protected $fillable = [
        'g_language_id',
        'g_language_support_id',
    ];

    public function language(): BelongsTo
    {
        return $this->belongsTo(GLanguage::class, 'g_language_id', 'id');
    }

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'g_language_support_id', 'id');
    }
}
