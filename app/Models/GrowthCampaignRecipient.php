<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GrowthCampaignRecipient extends Model
{
    protected $fillable = [
        'growth_campaign_id',
        'user_id',
        'notification_id',
        'variant',
        'channel',
        'sent_at',
        'notification_sent_at',
        'email_sent_at',
        'returned_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'notification_sent_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(GrowthCampaign::class, 'growth_campaign_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function notification(): BelongsTo
    {
        return $this->belongsTo(Notification::class);
    }
}
