<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\ChatJoinRequest;
use App\Models\User;
use Illuminate\Http\Request;

class ChatGroupAdminController extends Controller
{
    public function index()
    {
        $groups = ChatGroup::query()
            ->withCount('members')
            ->orderByDesc('created_at')
            ->get();

        return view('admin.chat.groups.index', compact('groups'));
    }

    public function create()
    {
        $users = User::query()
            ->orderBy('name')
            ->get();

        return view('admin.chat.groups.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_ids' => ['required', 'array', 'min:1'],
            'user_ids.*' => ['required', 'integer', 'exists:users,id'],
        ]);

        $admin = $request->user();

        $group = ChatGroup::create([
            'name' => $validated['name'],
            'created_by' => $admin->id,
        ]);

        // Tạo members theo danh sách chọn (Admin không tự động thêm nếu không tick)
        $now = now();
        foreach (array_unique($validated['user_ids']) as $userId) {
            ChatGroupMember::firstOrCreate(
                ['group_id' => $group->id, 'user_id' => (int) $userId],
                ['joined_at' => $now]
            );
        }

        return redirect()
            ->route('admin.chat.groups.index')
            ->with('status', 'Đã tạo nhóm chat thành công.');
    }

    public function show(ChatGroup $group)
    {
        $group->load(['members', 'messages.sender']);

        // Load 50 tin nhắn gần nhất (mục tiêu hiển thị nhanh)
        $messages = $group->messages()
            ->with('sender')
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        return view('admin.chat.groups.show', [
            'group' => $group,
            'messages' => $messages,
            'joinRequests' => ChatJoinRequest::query()
                ->where('group_id', $group->id)
                ->where('status', 'pending')
                ->with('user')
                ->orderByDesc('created_at')
                ->get(),
        ]);
    }

    public function edit(ChatGroup $group)
    {
        $users = User::query()
            ->orderBy('name')
            ->get();

        $memberIds = ChatGroupMember::query()
            ->where('group_id', $group->id)
            ->pluck('user_id')
            ->all();

        return view('admin.chat.groups.edit', [
            'group' => $group,
            'users' => $users,
            'memberIds' => $memberIds,
        ]);
    }

    public function update(Request $request, ChatGroup $group)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $group->update([
            'name' => $validated['name'],
        ]);

        // Nếu submit có user_ids thì cập nhật danh sách thành viên theo checkbox
        if (array_key_exists('user_ids', $validated)) {
            $userIds = array_values(array_unique(array_map('intval', $validated['user_ids'] ?? [])));
            $now = now();

            // xóa những user không còn trong danh sách
            ChatGroupMember::query()
                ->where('group_id', $group->id)
                ->whereNotIn('user_id', $userIds)
                ->delete();

            // thêm mới những user thiếu
            foreach ($userIds as $userId) {
                ChatGroupMember::firstOrCreate(
                    ['group_id' => $group->id, 'user_id' => $userId],
                    ['joined_at' => $now]
                );
            }
        }

        return redirect()
            ->route('admin.chat.groups.show', ['group' => $group->id])
            ->with('status', 'Đã cập nhật nhóm chat.');
    }

    public function destroy(ChatGroup $group)
    {
        $group->delete(); // cascade sẽ xóa members + messages

        return redirect()
            ->route('admin.chat.groups.index')
            ->with('status', 'Đã xóa nhóm chat.');
    }

    public function approveJoin(ChatJoinRequest $joinRequest)
    {
        if ($joinRequest->status !== 'pending') {
            return redirect()
                ->route('admin.chat.groups.show', ['group' => $joinRequest->group_id])
                ->with('status', 'Yêu cầu không ở trạng thái chờ.');
        }

        $admin = request()->user();

        $joinRequest->update([
            'status' => 'approved',
            'decided_at' => now(),
            'decided_by' => $admin->id,
        ]);

        ChatGroupMember::firstOrCreate(
            ['group_id' => $joinRequest->group_id, 'user_id' => $joinRequest->user_id],
            ['joined_at' => now()]
        );

        return redirect()
            ->route('admin.chat.groups.show', ['group' => $joinRequest->group_id])
            ->with('status', 'Đã duyệt yêu cầu tham gia nhóm.');
    }

    public function declineJoin(ChatJoinRequest $joinRequest)
    {
        if ($joinRequest->status !== 'pending') {
            return redirect()
                ->route('admin.chat.groups.show', ['group' => $joinRequest->group_id])
                ->with('status', 'Yêu cầu không ở trạng thái chờ.');
        }

        $admin = request()->user();

        $joinRequest->update([
            'status' => 'declined',
            'decided_at' => now(),
            'decided_by' => $admin->id,
        ]);

        return redirect()
            ->route('admin.chat.groups.show', ['group' => $joinRequest->group_id])
            ->with('status', 'Đã từ chối yêu cầu tham gia nhóm.');
    }
}

