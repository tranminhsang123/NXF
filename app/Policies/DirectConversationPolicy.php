<?php

namespace App\Policies;

use App\Models\DirectConversation;
use App\Models\User;

class DirectConversationPolicy
{
    public function view(User $user, DirectConversation $conversation): bool
    {
        return (int) $conversation->user_id === (int) $user->id
            || (int) $conversation->admin_id === (int) $user->id;
    }

    public function send(User $user, DirectConversation $conversation): bool
    {
        return $this->view($user, $conversation);
    }
}
