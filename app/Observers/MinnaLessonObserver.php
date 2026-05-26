<?php

namespace App\Observers;

use App\Models\MinnaLesson;
use App\Support\Cache\FlashcardCache;
use App\Support\Cache\InvalidationScheduler;
use App\Support\Cache\MinnaCache;
use Illuminate\Support\Facades\Cache;

class MinnaLessonObserver
{
    public function saved(MinnaLesson $lesson): void
    {
        if ($lesson->wasChanged('number')) {
            $orig = $lesson->getOriginal('number');
            if ($orig !== null) {
                Cache::forget('minna:lesson:'.$orig);
            }
        }

        InvalidationScheduler::once('minna.invalidate', function () {
            MinnaCache::flushAll();
            FlashcardCache::invalidate();
        });
    }

    public function deleted(MinnaLesson $lesson): void
    {
        InvalidationScheduler::once('minna.invalidate', function () {
            MinnaCache::flushAll();
            FlashcardCache::invalidate();
        });
    }
}
