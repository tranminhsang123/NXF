<?php

namespace App\Support\Cache;

use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use Illuminate\Support\Facades\Cache;

final class MinnaCache
{
    /**
     * Xóa toàn bộ key cache Minna liên quan (theo dữ liệu hiện tại trong DB).
     * Không gọi FlashcardCache — gọi từ InvalidationScheduler cùng FlashcardCache::invalidate() nếu cần.
     */
    public static function flushAll(): void
    {
        Cache::forget('minna:lessons:all');

        foreach (MinnaLesson::query()->pluck('number') as $number) {
            Cache::forget('minna:lesson:'.$number);
        }

        foreach (MinnaSection::query()->with('lesson:id,number')->get() as $section) {
            if ($section->lesson) {
                Cache::forget('minna:section:'.$section->lesson->number.':'.$section->key);
            }
        }

        Cache::forget('dashboard:total_minna_lessons');
        Cache::forget('dashboard:first_minna_lesson');
        DashboardCache::forgetAdminStats();
    }
}
