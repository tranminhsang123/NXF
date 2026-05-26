<?php

namespace App\Observers;

use App\Models\Kanji;
use App\Support\Cache\InvalidationScheduler;
use App\Support\Cache\KanjiCache;

class KanjiObserver
{
    public function saved(Kanji $kanji): void
    {
        InvalidationScheduler::once('kanji.invalidate', function () {
            KanjiCache::invalidate();
        });
    }

    public function deleted(Kanji $kanji): void
    {
        InvalidationScheduler::once('kanji.invalidate', function () {
            KanjiCache::invalidate();
        });
    }
}
