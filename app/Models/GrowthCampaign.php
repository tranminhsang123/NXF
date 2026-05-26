<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GrowthCampaign extends Model
{
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SENT = 'sent';

    public const CHANNEL_NOTIFICATION = 'notification';
    public const CHANNEL_EMAIL = 'email';
    public const CHANNEL_NOTIFICATION_EMAIL = 'notification_email';

    protected $fillable = [
        'created_by',
        'title',
        'message',
        'channel',
        'segment',
        'status',
        'audience_count',
        'metadata',
        'sent_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'sent_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(GrowthCampaignRecipient::class);
    }

    public static function channelLabels(): array
    {
        return [
            self::CHANNEL_NOTIFICATION => 'Thông báo trong app',
            self::CHANNEL_EMAIL => 'Email',
            self::CHANNEL_NOTIFICATION_EMAIL => 'Thông báo trong app + Email',
        ];
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_DRAFT => 'Bản nháp',
            self::STATUS_SENT => 'Đã gửi',
        ];
    }

    public function channelLabel(): string
    {
        return self::channelLabels()[$this->channel] ?? $this->channel;
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }
}
