<?php

namespace App\Http\Middleware;

use App\Models\SecuritySetting;
use Closure;
use Illuminate\Http\Request;

class FeatureAccessMiddleware
{
    private const FEATURE_LABELS = [
        'alphabet' => 'Bảng chữ cái',
        'kanji' => 'Ôn Kanji',
        'flashcard' => 'Flashcard',
        'vocabulary' => 'Từ vựng',
        'course' => 'Khóa học JLPT',
        'minna' => 'Minna no Nihongo',
    ];

    public function handle(Request $request, Closure $next, string $feature)
    {
        if (auth()->check()) {
            return $next($request);
        }

        $isLockedForGuest = SecuritySetting::getBool('feature_lock_' . $feature, true);
        if (! $isLockedForGuest) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Vui lòng đăng nhập để sử dụng tính năng này.',
                'feature' => $feature,
            ], 403);
        }

        $featureLabel = self::FEATURE_LABELS[$feature] ?? 'tính năng này';

        return redirect()
            ->guest(route('login'))
            ->with('warning', 'Vui lòng đăng nhập để sử dụng ' . $featureLabel . '.');
    }
}

