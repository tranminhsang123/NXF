<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        // API chung – tách rõ theo IP và user (trừ admin)
        RateLimiter::for('api', function (Request $request) {
            $ip = $request->ip() ?? 'unknown';
            $user = $request->user();
            $userId = $user?->id;
            $isAdmin = $user && ($user->role === 'admin');

            $limits = [
                // IP – 60 req / phút
                Limit::perMinute(60)->by('api:ip:'.$ip),
            ];

            if ($userId && ! $isAdmin) {
                // User – 60 req / phút
                $limits[] = Limit::perMinute(60)->by('api:user:'.$userId);
            }

            return $limits;
        });

        // Đăng nhập: chống brute force
        RateLimiter::for('login', function (Request $request) {
            $ip = $request->ip() ?? 'unknown';
            $email = (string) $request->input('email', '');

            return [
                // 5 lần / phút / IP
                Limit::perMinute(5)->by('login:ip:'.$ip),

                // 20 lần / 10 phút / IP
                Limit::perMinutes(10, 20)->by('login-10min:ip:'.$ip),

                // 10 lần / 30 phút / email
                Limit::perMinutes(30, 10)->by('login-30min:email:'.$email),
            ];
        });

        // Đăng ký tài khoản: chống spam tạo user ảo
        RateLimiter::for('register', function (Request $request) {
            $ip = $request->ip() ?? 'unknown';

            return [
                // 3 lần / 10 phút / IP
                Limit::perMinutes(10, 3)->by('register-10min:ip:'.$ip),

                // 10 lần / 60 phút / IP
                Limit::perMinutes(60, 10)->by('register-60min:ip:'.$ip),
            ];
        });

        // Học bài – các request GET nội dung (alphabet, kanji, minna, courses, ...)
        RateLimiter::for('study-get', function (Request $request) {
            $ip = $request->ip() ?? 'unknown';
            $user = $request->user();
            $userId = $user?->id;
            $isAdmin = $user && ($user->role === 'admin');

            $limits = [
                // IP – Burst: 120 request / 1 phút
                Limit::perMinute(120)->by('study-get:ip:'.$ip),

                // IP – Sustained: 600 request / 10 phút
                Limit::perMinutes(10, 600)->by('study-get-10min:ip:'.$ip),
            ];

            if ($userId && ! $isAdmin) {
                // User – Burst: 90 request / 1 phút
                $limits[] = Limit::perMinute(90)->by('study-get:user:'.$userId);

                // User – Sustained: 450 request / 10 phút
                $limits[] = Limit::perMinutes(10, 450)->by('study-get-10min:user:'.$userId);
            }

            return $limits;
        });

        // Học bài – các request POST lưu tiến trình / hoàn thành bài
        RateLimiter::for('study-post', function (Request $request) {
            $ip = $request->ip() ?? 'unknown';
            $user = $request->user();
            $userId = $user?->id;
            $isAdmin = $user && ($user->role === 'admin');

            $limits = [
                // IP – Burst: 30 request / 1 phút
                Limit::perMinute(30)->by('study-post:ip:'.$ip),

                // IP – Sustained: 120 request / 10 phút
                Limit::perMinutes(10, 120)->by('study-post-10min:ip:'.$ip),
            ];

            if ($userId && ! $isAdmin) {
                // User – Burst: 25 request / 1 phút
                $limits[] = Limit::perMinute(25)->by('study-post:user:'.$userId);

                // User – Sustained: 100 request / 10 phút
                $limits[] = Limit::perMinutes(10, 100)->by('study-post-10min:user:'.$userId);
            }

            return $limits;
        });

        // Chat write limiter riêng để giảm spam/flood
        RateLimiter::for('chat-write', function (Request $request) {
            $ip = $request->ip() ?? 'unknown';
            $user = $request->user();
            $userId = $user?->id;
            $isAdmin = $user && ($user->role === 'admin');

            $limits = [
                // IP burst-like guard
                Limit::perMinute(120)->by('chat-write:ip:'.$ip),
                // IP sustained guard
                Limit::perMinutes(10, 500)->by('chat-write-10min:ip:'.$ip),
            ];

            if ($userId && ! $isAdmin) {
                // Per-user burst-like guard
                $limits[] = Limit::perMinute(60)->by('chat-write:user:'.$userId);
                // Per-user sustained guard
                $limits[] = Limit::perMinutes(10, 240)->by('chat-write-10min:user:'.$userId);
            }

            return $limits;
        });

        // Báo cáo vi phạm DevTools (F12, Ctrl+Shift+I/J, Ctrl+U)
        RateLimiter::for('devtools-violation', function (Request $request) {
            $user = $request->user();
            $key = $user ? 'devtools:user:'.$user->id : 'devtools:ip:'.($request->ip() ?? 'unknown');

            return [Limit::perMinute(20)->by($key)];
        });

        // Admin panel
        RateLimiter::for('admin', function (Request $request) {
            $ip = $request->ip() ?? 'unknown';

            return [
                // 60 request / 1 phút / IP
                Limit::perMinute(60)->by('admin:ip:'.$ip),

                // 200 request / 10 phút / IP
                Limit::perMinutes(10, 200)->by('admin-10min:ip:'.$ip),
            ];
        });

        // Global fallback theo IP – chặn bot cực nặng
        RateLimiter::for('global', function (Request $request) {
            $ip = $request->ip() ?? 'unknown';
            $user = $request->user();
            $userId = $user?->id;
            $isAdmin = $user && ($user->role === 'admin');

            $limits = [
                // IP – Burst: 180 request / 1 phút
                Limit::perMinute(180)->by('global:ip:'.$ip),

                // IP – Sustained: 800 request / 10 phút
                Limit::perMinutes(10, 800)->by('global-10min:ip:'.$ip),
            ];

            if ($userId && ! $isAdmin) {
                // User-level global limiter – bảo vệ khi một account spam trên nhiều IP (trừ admin)
                // User – Burst: 140 request / 1 phút
                $limits[] = Limit::perMinute(140)->by('global:user:'.$userId);

                // User – Sustained: 650 request / 10 phút
                $limits[] = Limit::perMinutes(10, 650)->by('global-10min:user:'.$userId);
            }

            return $limits;
        });

        // Register route model binding for course-data
        Route::bind('course_datum', function ($value) {
            return \App\Models\N5CourseData::findOrFail($value);
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            // Toàn bộ web routes sẽ có thêm global rate limiting theo IP
            Route::middleware(['web', 'throttle:global'])
                ->group(base_path('routes/web.php'));
        });
    }
}
