<?php

namespace App\Events;

use App\Models\DirectMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DirectMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public DirectMessage $message
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('App.Models.User.'.$this->message->sender_id),
            new PrivateChannel('App.Models.User.'.$this->message->recipient_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'direct.message.sent';
    }

    public function broadcastWith(): array
    {
        $this->message->loadMissing('sender');

        return [
            'id' => $this->message->id,
            'message_uuid' => $this->message->message_uuid,
            'event_id' => $this->message->event_id,
            'parent_event_id' => $this->message->parent_event_id,
            'status' => $this->message->event_status,
            'conversation_id' => $this->message->conversation_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender?->name,
            'recipient_id' => $this->message->recipient_id,
            'content' => $this->message->content,
            'read_at' => $this->message->read_at?->toIso8601String(),
            'created_at' => $this->message->created_at?->toIso8601String(),
        ];
    }
}
