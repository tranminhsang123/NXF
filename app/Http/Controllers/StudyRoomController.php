<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Services\PersonalizedRoadmapService;
use Illuminate\Http\Request;

class StudyRoomController extends Controller
{
    public function index(Request $request, PersonalizedRoadmapService $roadmapService)
    {
        $user = $request->user();

        $groups = ChatGroup::query()
            ->whereHas('members', fn ($query) => $query->where('chat_group_members.user_id', $user->id))
            ->withCount(['members', 'messages'])
            ->orderByDesc('updated_at')
            ->get();

        $availableGroups = ChatGroup::query()
            ->whereDoesntHave('members', fn ($query) => $query->where('chat_group_members.user_id', $user->id))
            ->withCount(['members', 'messages'])
            ->orderByDesc('created_at')
            ->take(6)
            ->get();

        $roadmap = $roadmapService->build($user);
        $nextSection = $roadmap['next_section'] ?? null;

        return view('user.study-room', [
            'user' => $user,
            'groups' => $groups,
            'availableGroups' => $availableGroups,
            'nextSection' => $nextSection,
            'nextUrl' => $nextSection
                ? route('minna.section', [
                    'number' => $nextSection['lesson_number'] ?? 1,
                    'sectionKey' => $nextSection['section_key'] ?? '',
                ])
                : route('minna.index'),
        ]);
    }
}
