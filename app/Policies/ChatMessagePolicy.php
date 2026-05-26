<?php

namespace App\Policies;

use App\Models\ChatGroupMember;
use App\Models\ChatMessage;
use App\Models\User;

class ChatMessagePolicy
{
    public function view(User $user, ChatMessage $message): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return ChatGroupMember::query()
            ->where('group_id', $message->group_id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function update(User $user, ChatMessage $message): bool
    {
        return (int) $message->sender_id === (int) $user->id;
    }

    public function delete(User $user, ChatMessage $message): bool
    {
        return (int) $message->sender_id === (int) $user->id;
    }

    public function forward(User $user, ChatMessage $message): bool
    {
        return $this->view($user, $message);
    }
}
