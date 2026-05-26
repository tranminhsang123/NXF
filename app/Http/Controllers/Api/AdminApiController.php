<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\ChatJoinRequest;
use App\Models\Kanji;
use App\Models\MinnaSection;
use App\Models\Notification;
use App\Models\NotificationRead;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminApiController extends Controller
{
    public function dashboard(): JsonResponse
    {
        $this->authorizeAdmin(request());

        $stats = Cache::remember('admin:api:dashboard:stats', 120, function () {
            return [
                'total_users' => User::count(),
                'total_kanjis' => Kanji::count(),
                'total_groups' => ChatGroup::count(),
                'pending_join_requests' => ChatJoinRequest::query()->where('status', 'pending')->count(),
            ];
        });

        return response()->json($stats);
    }

    public function users(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $perPage = max(1, min((int) $request->query('per_page', 20), 100));

        $query = User::query();
        if ($request->filled('search')) {
            $search = (string) $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%'.$search.'%')
                    ->orWhere('email', 'like', '%'.$search.'%');
            });
        }
        if ($request->filled('role')) {
            $query->where('role', (string) $request->query('role'));
        }

        $users = $query->orderByDesc('created_at')->paginate($perPage);

        return response()->json($users);
    }

    public function lockUser(Request $request, User $user): JsonResponse
    {
        $this->authorizeAdmin($request);
        if ($request->user()->id === $user->id) {
            return response()->json(['message' => 'Khong the khoa chinh minh.'], 422);
        }

        $user->update([
            'locked_at' => now(),
            'locked_reason' => (string) $request->input('reason', 'Khoa boi admin.'),
        ]);

        return response()->json(['message' => 'Da khoa tai khoan.']);
    }

    public function unlockUser(User $user): JsonResponse
    {
        $this->authorizeAdmin(request());
        $user->update([
            'locked_at' => null,
            'locked_reason' => null,
        ]);

        return response()->json(['message' => 'Da mo khoa tai khoan.']);
    }

    public function notifications(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $admin = $request->user();
        $perPage = max(1, min((int) $request->query('per_page', 20), 100));

        $notifications = Notification::query()
            ->where(function ($q) use ($admin) {
                $q->whereNull('user_id')->orWhere('user_id', $admin->id);
            })
            ->with(['reads' => fn ($q) => $q->where('user_id', $admin->id)])
            ->orderByDesc('created_at')
            ->paginate($perPage);

        return response()->json($notifications);
    }

    public function markNotificationRead(Request $request, Notification $notification): JsonResponse
    {
        $this->authorizeAdmin($request);
        $admin = $request->user();
        if (! ($notification->user_id === null || (int) $notification->user_id === (int) $admin->id)) {
            return response()->json(['message' => 'Notification khong hop le.'], 404);
        }

        NotificationRead::firstOrCreate(
            ['notification_id' => $notification->id, 'user_id' => $admin->id],
            ['read_at' => now()]
        );

        return response()->json(['ok' => true]);
    }

    public function markAllNotificationsRead(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $admin = $request->user();

        $notificationIds = Notification::query()
            ->where(function ($q) use ($admin) {
                $q->whereNull('user_id')->orWhere('user_id', $admin->id);
            })
            ->pluck('id');

        foreach ($notificationIds as $notificationId) {
            NotificationRead::firstOrCreate(
                ['notification_id' => $notificationId, 'user_id' => $admin->id],
                ['read_at' => now()]
            );
        }

        return response()->json(['ok' => true, 'count' => $notificationIds->count()]);
    }

    public function moderation(Request $request): JsonResponse
    {
        $this->authorizeAdmin($request);
        $perPage = max(1, min((int) $request->query('per_page', 20), 100));

        $kanji = Kanji::query()->orderBy('level')->orderBy('character')->paginate($perPage, ['*'], 'kanji_page');
        $minnaSections = MinnaSection::query()
            ->with('lesson:id,number,title')
            ->orderByDesc('updated_at')
            ->paginate($perPage, ['*'], 'section_page');
        $joinRequests = ChatJoinRequest::query()
            ->with(['group:id,name', 'user:id,name,email'])
            ->where('status', 'pending')
            ->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'join_request_page');

        return response()->json([
            'kanji' => $kanji,
            'minna_sections' => $minnaSections,
            'join_requests' => $joinRequests,
        ]);
    }

    public function updateKanji(Request $request, Kanji $kanji): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'character' => ['required', 'string', 'max:10'],
            'meaning' => ['required', 'string', 'max:255'],
            'on_reading' => ['nullable', 'string', 'max:255'],
            'kun_reading' => ['nullable', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:10'],
            'stroke_count' => ['nullable', 'integer'],
            'radical' => ['nullable', 'string', 'max:50'],
            'examples' => ['nullable'],
        ]);

        $kanji->update($data);

        return response()->json(['message' => 'Da cap nhat kanji.', 'kanji' => $kanji]);
    }

    public function updateMinnaSection(Request $request, MinnaSection $section): JsonResponse
    {
        $this->authorizeAdmin($request);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable'],
            'media_url' => ['nullable', 'string', 'max:255'],
        ]);

        $section->update($data);

        return response()->json([
            'message' => 'Da cap nhat section.',
            'section' => $section->fresh(),
        ]);
    }

    public function approveJoinRequest(Request $request, ChatJoinRequest $joinRequest): JsonResponse
    {
        $this->authorizeAdmin($request);
        $joinRequest->update([
            'status' => 'approved',
            'decided_at' => now(),
            'decided_by' => $request->user()->id,
        ]);

        ChatGroupMember::firstOrCreate(
            ['group_id' => $joinRequest->group_id, 'user_id' => $joinRequest->user_id],
            ['joined_at' => now()]
        );

        return response()->json(['message' => 'Da duyet yeu cau.']);
    }

    public function declineJoinRequest(Request $request, ChatJoinRequest $joinRequest): JsonResponse
    {
        $this->authorizeAdmin($request);
        $joinRequest->update([
            'status' => 'declined',
            'decided_at' => now(),
            'decided_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Da tu choi yeu cau.']);
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless($request->user() && $request->user()->role === 'admin', 403);
    }
}
