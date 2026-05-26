<?php

namespace App\Http\Middleware;

use App\Models\SecuritySetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserNotLocked
{
    /**
     * Nếu user đã đăng nhập nhưng bị khóa → đăng xuất và chuyển về trang login.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->refresh();
            if ($user->isLocked()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                $message = trim((string) SecuritySetting::get('devtools_lock_message', '')) ?: 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.';

                return redirect()->route('login')->withErrors(['email' => $message]);
            }
        }

        return $next($request);
    }
}
