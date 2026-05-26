<?php

namespace App\Services;

use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class MinnaService
{
    private const CACHE_TTL = 600;

    /**
     * Lấy danh sách tất cả các bài học
     */
    public function getAllLessons()
    {
        $lessons = Cache::remember('minna:lessons:all', self::CACHE_TTL, function () {
            return $this->queryAllLessons();
        });

        if ($lessons->isEmpty() && MinnaLesson::query()->exists()) {
            Cache::forget('minna:lessons:all');

            return $this->queryAllLessons();
        }

        return $lessons;
    }

    /**
     * Lấy bài học theo số thứ tự
     */
    public function getLessonByNumber(int $number): MinnaLesson
    {
        $cacheKey = "minna:lesson:{$number}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($number) {
            $lesson = MinnaLesson::select('id', 'number', 'title', 'description')
                ->published()
                ->where('number', $number)
                ->with(['sections' => function ($query) {
                    $query->select('id', 'lesson_id', 'order_index', 'key', 'title', 'content', 'media_url')
                        ->published()
                        ->orderBy('order_index');
                }])
                ->first();

            if (!$lesson) {
                throw new InvalidArgumentException('Không tìm thấy bài học');
            }

            return $lesson;
        });
    }

    /**
     * Nhóm sections theo key
     */
    public function groupSectionsByKey($sections)
    {
        return $sections->groupBy('key');
    }

    /**
     * Lấy số bài học trước đó
     */
    public function getPreviousLessonNumber(int $currentNumber): ?int
    {
        return MinnaLesson::where('number', '<', $currentNumber)
            ->published()
            ->orderByDesc('number')
            ->value('number');
    }

    /**
     * Lấy số bài học tiếp theo
     */
    public function getNextLessonNumber(int $currentNumber): ?int
    {
        return MinnaLesson::where('number', '>', $currentNumber)
            ->published()
            ->orderBy('number')
            ->value('number');
    }

    /**
     * Lấy section theo lesson number và section key
     */
    public function getSectionByLessonAndKey(int $lessonNumber, string $sectionKey): MinnaSection
    {
        $cacheKey = "minna:section:{$lessonNumber}:{$sectionKey}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($lessonNumber, $sectionKey) {
            $section = MinnaSection::select('id', 'lesson_id', 'order_index', 'key', 'title', 'content', 'media_url')
                ->published()
                ->whereHas('lesson', function ($query) use ($lessonNumber) {
                    $query->published()->where('number', $lessonNumber);
                })
                ->where('key', $sectionKey)
                ->with('lesson:id,number,title,description')
                ->first();

            if (!$section) {
                throw new InvalidArgumentException('Không tìm thấy section');
            }

            return $section;
        });
    }

    private function queryAllLessons()
    {
        return MinnaLesson::select('id', 'number', 'title', 'description')
            ->published()
            ->orderBy('number')
            ->get();
    }
}
