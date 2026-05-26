<?php

namespace App\Observers;

use App\Models\N5CourseData;
use App\Support\Cache\CourseN5Cache;
use App\Support\Cache\InvalidationScheduler;

class N5CourseDataObserver
{
    public function saved(N5CourseData $n5CourseData): void
    {
        InvalidationScheduler::once('course_n5.invalidate', function () {
            CourseN5Cache::invalidateAll();
        });
    }

    public function deleted(N5CourseData $n5CourseData): void
    {
        InvalidationScheduler::once('course_n5.invalidate', function () {
            CourseN5Cache::invalidateAll();
        });
    }
}
