<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GameGFranchise extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'g_franchise_id',
        'main_franchise',
        'updated_at',
    ];
    protected $casts = [
        'main_franchise' => 'boolean',
    ];

    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }

    public function g_franchise(): BelongsTo
    {
        return $this->belongsTo(GFranchise::class, 'g_franchise_id', 'id');
    }
}
