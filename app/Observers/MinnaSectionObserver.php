<?php

namespace App\Observers;

use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Support\Cache\FlashcardCache;
use App\Support\Cache\InvalidationScheduler;
use App\Support\Cache\MinnaCache;
use Illuminate\Support\Facades\Cache;

class MinnaSectionObserver
{
    public function saved(MinnaSection $section): void
    {
        if ($section->wasChanged(['lesson_id', 'key'])) {
            $this->forgetStaleSectionKeys($section);
        }

        InvalidationScheduler::once('minna.invalidate', function () {
            MinnaCache::flushAll();
            FlashcardCache::invalidate();
        });
    }

    public function deleted(MinnaSection $section): void
    {
        InvalidationScheduler::once('minna.invalidate', function () {
            MinnaCache::flushAll();
            FlashcardCache::invalidate();
        });
    }

    private function forgetStaleSectionKeys(MinnaSection $section): void
    {
        $origLessonId = $section->getOriginal('lesson_id');
        $origKey = $section->getOriginal('key');
        if ($origLessonId === null || $origKey === null) {
            return;
        }
        $lesson = MinnaLesson::query()->find($origLessonId);
        if ($lesson) {
            Cache::forget('minna:section:'.$lesson->number.':'.$origKey);
        }
    }
}
