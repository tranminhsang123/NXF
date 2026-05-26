<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DevtoolsViolation extends Model
{
    public const TYPE_F12 = 'f12';
    public const TYPE_CTRL_SHIFT_I = 'ctrl_shift_i';
    public const TYPE_CTRL_SHIFT_J = 'ctrl_shift_j';
    public const TYPE_CTRL_U = 'ctrl_u';

    protected $fillable = [
        'user_id',
        'violation_type',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function typeLabel(string $type): string
    {
        return match ($type) {
            self::TYPE_F12 => 'F12',
            self::TYPE_CTRL_SHIFT_I => 'Ctrl+Shift+I',
            self::TYPE_CTRL_SHIFT_J => 'Ctrl+Shift+J',
            self::TYPE_CTRL_U => 'Ctrl+U',
            default => $type,
        };
    }
}
