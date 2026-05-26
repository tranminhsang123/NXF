<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public ChatMessage $message
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat-group.'.$this->message->group_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.message.sent';
    }

    public function broadcastWith(): array
    {
        $this->message->loadMissing('sender');
        $this->message->loadMissing('repliedMessage.sender');

        return [
            'id' => $this->message->id,
            'message_uuid' => $this->message->message_uuid,
            'event_id' => $this->message->event_id,
            'parent_event_id' => $this->message->parent_event_id,
            'status' => $this->message->event_status,
            'group_id' => $this->message->group_id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender?->name,
            'content' => $this->message->content,
            'reply_to' => $this->message->repliedMessage ? [
                'id' => $this->message->repliedMessage->id,
                'sender_name' => $this->message->repliedMessage->sender?->name,
                'content' => $this->message->repliedMessage->content,
            ] : null,
            'created_at' => $this->message->created_at?->toIso8601String(),
        ];
    }
}
