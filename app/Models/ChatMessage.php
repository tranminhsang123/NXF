<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_id',
        'sender_id',
        'message_uuid',
        'content',
        'client_message_id',
        'event_id',
        'parent_event_id',
        'event_status',
        'event_retry_count',
        'next_retry_at',
        'event_last_error',
        'edited_at',
        'reply_to_message_id',
        'forwarded_from_message_id',
        'forwarded_from_group_id',
        'forwarded_from_sender_name',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
        'event_retry_count' => 'integer',
        'next_retry_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ChatGroup::class, 'group_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function repliedMessage(): BelongsTo
    {
        return $this->belongsTo(self::class, 'reply_to_message_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(ChatMessageReport::class, 'chat_message_id');
    }
}
