<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\ChatGroupMember;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private channel cho chat nhóm (chỉ thành viên nhóm mới listen được)
Broadcast::channel('chat-group.{groupId}', function ($user, $groupId) {
    return ChatGroupMember::query()
        ->where('group_id', (int) $groupId)
        ->where('user_id', (int) $user->id)
        ->exists();
});
