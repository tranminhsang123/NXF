<?php

namespace App\Support\Cache;

use App\Services\AlphabetService;
use App\Services\KanjiService;
use Illuminate\Support\Facades\Cache;

final class KanjiCache
{
    /** Gọi sau mọi thay đổi bảng kanjis (user + alphabet kanji cache). */
    public static function invalidate(): void
    {
        Cache::forget('kanji:counts_by_level');
        foreach (KanjiService::LEVELS as $level) {
            Cache::forget('kanji:by_level:'.$level);
        }
        Cache::forget('dashboard:total_kanjis');
        DashboardCache::forgetAdminStats();
        AlphabetService::clearAlphabetCache();
    }
}
