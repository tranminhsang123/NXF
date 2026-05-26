<?php

namespace App\Policies;

use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\User;

class ChatGroupPolicy
{
    public function view(User $user, ChatGroup $group): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return ChatGroupMember::query()
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->exists();
    }

    public function send(User $user, ChatGroup $group): bool
    {
        return $this->view($user, $group);
    }
}
