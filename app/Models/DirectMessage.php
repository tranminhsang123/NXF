<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DirectMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'recipient_id',
        'message_uuid',
        'content',
        'client_message_id',
        'event_id',
        'parent_event_id',
        'event_status',
        'event_retry_count',
        'next_retry_at',
        'event_last_error',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'event_retry_count' => 'integer',
        'next_retry_at' => 'datetime',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(DirectConversation::class, 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
