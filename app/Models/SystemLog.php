<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLog extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'message',
        'context',
    ];

    protected $casts = [
        'context' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function add(?User $user, string $type, string $message, array $context = []): void
    {
        static::create([
            'user_id' => $user?->id,
            'type' => $type,
            'message' => $message,
            'context' => $context ?: null,
        ]);
    }
}

