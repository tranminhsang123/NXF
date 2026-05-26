<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FavoriteItem extends Model
{
    protected $fillable = [
        'user_id',
        'item_key',
        'item_type',
        'front',
        'back',
        'source_type',
        'source_id',
        'lesson_number',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function keyFor(int $userId, string $front, string $back): string
    {
        return hash('sha256', $userId.'|'.trim($front).'|'.trim($back));
    }
}
