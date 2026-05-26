<?php

namespace App\Support\Cache;

use Illuminate\Support\Facades\Cache;

final class FlashcardCache
{
    private const VERSION_KEY = 'flashcard:base_version';

    /** Phiên bản dùng trong key cache flashcards:base (invalidate không cần quét mọi tổ hợp bài). */
    public static function currentBaseVersion(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }

    public static function invalidate(): void
    {
        InvalidationScheduler::once('flashcard.invalidate', function () {
            Cache::forget('flashcard:lessons');
            $v = self::currentBaseVersion();
            Cache::put(self::VERSION_KEY, $v + 1, now()->addYears(10));
        });
    }
}
