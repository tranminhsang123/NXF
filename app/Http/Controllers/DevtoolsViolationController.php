<?php

namespace App\Http\Controllers;

use App\Models\DevtoolsViolation;
use App\Models\Notification;
use App\Models\SecuritySetting;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DevtoolsViolationController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'violation_type' => ['required', 'string', 'in:f12,ctrl_shift_i,ctrl_shift_j,ctrl_u'],
        ]);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($user->role === 'admin') {
            return response()->json(['message' => 'Ignored for admin'], 200);
        }

        if ($user->isLocked()) {
            return response()->json(['message' => 'Account locked'], 403);
        }

        $logEnabled = SecuritySetting::getBool('devtools_log_enabled', true);
        if ($logEnabled) {
            DevtoolsViolation::create([
                'user_id' => $user->id,
                'violation_type' => $request->violation_type,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        $lockAfter = SecuritySetting::getInt('devtools_lock_after_violations', 1);
        $windowHours = SecuritySetting::getInt('devtools_violation_window_hours', 24);
        if ($lockAfter > 0) {
            $since = now()->subHours($windowHours);
            $count = DevtoolsViolation::where('user_id', $user->id)->where('created_at', '>=', $since)->count();
            if ($count >= $lockAfter) {
                $lockMessage = SecuritySetting::get('devtools_lock_message', 'Tài khoản đã bị khóa do vi phạm quy định. Vui lòng liên hệ quản trị viên.');
                $user->update([
                    'locked_at' => now(),
                    'locked_reason' => $lockMessage,
                ]);
                Notification::createForAdmins('user_locked', 'Tài khoản bị khóa', $user->name . ' (' . $user->email . ') đã bị khóa do vi phạm DevTools.', ['user_id' => $user->id]);
                SystemLog::add($user, 'user_locked', $user->name . ' (' . $user->email . ') bị khóa do vi phạm DevTools.', ['source' => 'devtools']);
                return response()->json(['message' => 'Violation logged', 'locked' => true], 200);
            }
        }

        return response()->json(['message' => 'Violation logged', 'locked' => false], 200);
    }
}
