<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\NotificationRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Danh sách thông báo cho admin
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $notifications = Notification::forAdmin($user, 20);

        return view('admin.notifications.index', compact('notifications'));
    }

    /**
     * Đánh dấu một thông báo đã đọc
     */
    public function markRead(Notification $notification)
    {
        $user = Auth::user();

        if (!$this->canAccess($notification, $user)) {
            abort(404);
        }

        NotificationRead::firstOrCreate(
            [
                'notification_id' => $notification->id,
                'user_id' => $user->id,
            ],
            ['read_at' => now()]
        );

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Đã đánh dấu đã đọc.');
    }

    /**
     * Đánh dấu tất cả thông báo đã đọc
     */
    public function markAllRead()
    {
        $user = Auth::user();

        $notificationIds = Notification::query()
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $user->id))
            ->pluck('id');

        foreach ($notificationIds as $id) {
            NotificationRead::firstOrCreate(
                [
                    'notification_id' => $id,
                    'user_id' => $user->id,
                ],
                ['read_at' => now()]
            );
        }

        if (request()->wantsJson()) {
            return response()->json(['ok' => true, 'count' => $notificationIds->count()]);
        }

        return back()->with('success', 'Đã đánh dấu tất cả đã đọc.');
    }

    private function canAccess(Notification $notification, $user): bool
    {
        return $notification->user_id === null || $notification->user_id === $user->id;
    }
}
