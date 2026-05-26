<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\ChatGroupMember;
use App\Models\ChatJoinRequest;

class ChatGroupController extends Controller
{
    public function index()
    {
        $userId = request()->user()->id;

        $groups = ChatGroup::query()
            ->whereHas('members', fn ($q) => $q->where('chat_group_members.user_id', $userId))
            ->with(['messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('updated_at')
            ->get();

        $availableGroups = ChatGroup::query()
            ->whereDoesntHave('members', fn ($q) => $q->where('chat_group_members.user_id', $userId))
            ->with(['messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('created_at')
            ->get();

        return view('chat.index', [
            'groups' => $groups,
            'availableGroups' => $availableGroups,
        ]);
    }

    public function show(ChatGroup $group)
    {
        $user = request()->user();
        $isMember = ChatGroupMember::query()
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->exists();

        if (! $isMember) {
            abort(403, 'Bạn không có quyền truy cập cuộc chat này.');
        }

        // Load 50 tin nhắn gần nhất (để hiển thị theo thứ tự cũ -> mới)
        $messages = ChatMessage::query()
            ->where('group_id', $group->id)
            ->with(['sender', 'repliedMessage.sender'])
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        // Danh sách nhóm của user để hiển thị sidebar trong màn chat
        $userGroups = ChatGroup::query()
            ->whereHas('members', fn ($q) => $q->where('chat_group_members.user_id', $user->id))
            ->with(['messages' => function ($q) {
                $q->latest()->limit(1);
            }])
            ->orderByDesc('updated_at')
            ->get();

        return view('chat.show', [
            'group' => $group,
            'messages' => $messages,
            'userGroups' => $userGroups,
        ]);
    }

    public function requestToJoin(ChatGroup $group)
    {
        $user = request()->user();

        $alreadyMember = ChatGroupMember::query()
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($alreadyMember) {
            return redirect()->route('chat.index')
                ->with('status', 'Bạn đã là thành viên của nhóm này.');
        }

        // Nếu đã có request pending thì không tạo thêm
        $existing = ChatJoinRequest::query()
            ->where('group_id', $group->id)
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->first();

        if ($existing) {
            return redirect()->route('chat.index')
                ->with('status', 'Bạn đã có yêu cầu tham gia nhóm trước đó.');
        }

        ChatJoinRequest::create([
            'group_id' => $group->id,
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        return redirect()->route('chat.index')
            ->with('status', 'Đã gửi yêu cầu tham gia nhóm. Admin sẽ duyệt.');
    }
}

