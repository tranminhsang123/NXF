<?php

namespace App\Services;

use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\User;
use App\Models\UserMinnaSectionProgress;
use App\Models\UserProgress;
use Carbon\Carbon;

class UserProgressService
{
    public function __construct(
        private GamificationService $gamification
    ) {}

    /**
     * Cập nhật tiến độ khi user mở một bài Minna.
     */
    public function touchMinnaLesson(User $user, MinnaLesson $lesson): UserProgress
    {
        // Sử dụng khóa duy nhất theo schema mới: user_id + lesson_type + lesson_id
        $progress = UserProgress::firstOrNew([
            'user_id' => $user->id,
            'lesson_type' => UserProgress::TYPE_MINNA,
            'lesson_id' => $lesson->id,
        ]);

        $progress->last_accessed_at = Carbon::now();

        if ($progress->status !== UserProgress::STATUS_COMPLETED) {
            $progress->status = UserProgress::STATUS_IN_PROGRESS;
        }

        $progress->save();

        return $progress;
    }

    /**
     * Đánh dấu một bài Minna là đã hoàn thành.
     */
    public function markMinnaLessonCompleted(User $user, MinnaLesson $lesson): UserProgress
    {
        $wasAlreadyCompleted = UserProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->where('lesson_id', $lesson->id)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->exists();

        $progress = UserProgress::firstOrNew([
            'user_id' => $user->id,
            'lesson_type' => UserProgress::TYPE_MINNA,
            'lesson_id' => $lesson->id,
        ]);

        $progress->status = UserProgress::STATUS_COMPLETED;
        $progress->last_accessed_at = Carbon::now();
        $progress->completed_at = Carbon::now();

        $progress->save();

        $lesson->sections()
            ->select('id', 'lesson_id', 'key')
            ->get()
            ->each(fn (MinnaSection $section) => $this->markMinnaSectionCompleted($user, $section, false));

        if (! $wasAlreadyCompleted) {
            $this->gamification->onMinnaLessonCompleted($user);
        }

        return $progress;
    }

    public function markMinnaSectionCompleted(User $user, MinnaSection $section, bool $syncLesson = true): UserMinnaSectionProgress
    {
        $lesson = $section->lesson ?: MinnaLesson::query()->findOrFail($section->lesson_id);
        $now = Carbon::now();

        $this->touchMinnaLesson($user, $lesson);

        $sectionProgress = UserMinnaSectionProgress::query()->firstOrNew([
            'user_id' => $user->id,
            'minna_section_id' => $section->id,
        ]);

        $alreadyCompleted = $sectionProgress->exists
            && $sectionProgress->status === UserProgress::STATUS_COMPLETED;

        $sectionProgress->fill([
            'minna_lesson_id' => $lesson->id,
            'section_key' => $section->key,
            'status' => UserProgress::STATUS_COMPLETED,
            'last_accessed_at' => $now,
            'completed_at' => $sectionProgress->completed_at ?: $now,
        ]);
        $sectionProgress->save();

        if ($syncLesson && ! $alreadyCompleted) {
            $this->gamification->onMinnaSectionCompleted($user);
        }

        if ($syncLesson) {
            $this->syncMinnaLessonStatusFromSections($user, $lesson);
        }

        return $sectionProgress;
    }

    public function getMinnaLessonSectionSummary(User $user, MinnaLesson $lesson): array
    {
        $total = $lesson->sections()->count();
        $completed = UserMinnaSectionProgress::query()
            ->where('user_id', $user->id)
            ->where('minna_lesson_id', $lesson->id)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ];
    }

    public function syncMinnaLessonStatusFromSections(User $user, MinnaLesson $lesson): UserProgress
    {
        $progress = $this->touchMinnaLesson($user, $lesson);
        $summary = $this->getMinnaLessonSectionSummary($user, $lesson);

        if ($summary['total'] > 0 && $summary['completed'] >= $summary['total']) {
            if ($progress->status !== UserProgress::STATUS_COMPLETED) {
                $progress->status = UserProgress::STATUS_COMPLETED;
                $progress->completed_at = $progress->completed_at ?: Carbon::now();
                $progress->save();
                $this->gamification->onMinnaLessonCompleted($user);
            }
        }

        return $progress;
    }
}
