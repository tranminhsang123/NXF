<?php

namespace App\Services;

use App\Models\FlashcardCardState;
use App\Models\MinnaQuizAttempt;
use App\Models\User;
use App\Models\UserProgress;
use Carbon\Carbon;

class WeakLessonSuggestionService
{
    public function suggest(User $user): array
    {
        $weakQuiz = MinnaQuizAttempt::query()
            ->where('user_id', $user->id)
            ->where('percent', '<', 80)
            ->with('lesson:id,number,title')
            ->latest('completed_at')
            ->take(3)
            ->get()
            ->map(fn (MinnaQuizAttempt $attempt) => [
                'lesson_number' => $attempt->lesson?->number,
                'lesson_title' => $attempt->lesson?->title,
                'percent' => (int) $attempt->percent,
                'completed_at' => $attempt->completed_at,
                'url' => $attempt->lesson
                    ? route('minna.show', ['number' => $attempt->lesson->number])
                    : route('minna.index'),
            ])
            ->values()
            ->all();

        $weakFlashcards = FlashcardCardState::query()
            ->where('user_id', $user->id)
            ->where(function ($query) {
                $query->where('last_quality', '<', 3)
                    ->orWhere('lapses', '>', 0)
                    ->orWhere('next_review_at', '<=', now());
            })
            ->with('minnaSection.lesson:id,number,title')
            ->orderByDesc('lapses')
            ->orderBy('next_review_at')
            ->take(5)
            ->get()
            ->map(function (FlashcardCardState $state) {
                $lesson = $state->minnaSection?->lesson;

                return [
                    'lesson_number' => $lesson?->number,
                    'lesson_title' => $lesson?->title,
                    'lapses' => (int) ($state->lapses ?? 0),
                    'last_quality' => $state->last_quality,
                    'url' => route('flashcard.index'),
                ];
            })
            ->values()
            ->all();

        return [
            'weak_quiz' => $weakQuiz,
            'weak_flashcards' => $weakFlashcards,
            'inactive_review' => $this->inactiveReview($user),
            'behavior_profile' => $this->behaviorProfile($user, $weakQuiz, $weakFlashcards),
        ];
    }

    private function inactiveReview(User $user): ?array
    {
        if (! $user->last_study_date) {
            return null;
        }

        $daysAway = Carbon::parse($user->last_study_date)->startOfDay()->diffInDays(Carbon::today());
        if ($daysAway < 3 || $daysAway > 7) {
            return null;
        }

        return [
            'days_away' => $daysAway,
            'title' => 'Bài ôn siêu ngắn sau '.$daysAway.' ngày nghỉ',
            'message' => 'Chỉ cần ôn SRS hoặc mở lại bài gần nhất trong 5 phút để lấy nhịp lại.',
            'url' => route('flashcard.index'),
        ];
    }

    private function behaviorProfile(User $user, array $weakQuiz, array $weakFlashcards): array
    {
        $completedLessons = UserProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->count();

        $recentAttempts = MinnaQuizAttempt::query()
            ->where('user_id', $user->id)
            ->latest('completed_at')
            ->take(5)
            ->get(['percent']);

        $avgQuiz = $recentAttempts->isNotEmpty()
            ? (int) round($recentAttempts->avg('percent'))
            : null;

        $tags = [];
        if ($completedLessons === 0) {
            $tags[] = 'Người mới: ưu tiên bài 5 phút';
        }
        if ($avgQuiz !== null && $avgQuiz < 75) {
            $tags[] = 'Quiz còn yếu: cần ôn lỗi sai';
        }
        if ($weakFlashcards !== []) {
            $tags[] = 'SRS có thẻ dễ quên';
        }
        if ((int) ($user->current_streak ?? 0) >= 7) {
            $tags[] = 'Streak tốt: có thể tăng độ khó nhẹ';
        }

        return [
            'title' => $tags === [] ? 'Nhịp học ổn định' : 'Cá nhân hóa theo hành vi học',
            'summary' => $tags === []
                ? 'Chưa có dấu hiệu yếu rõ ràng, hệ thống tiếp tục đề xuất bài mới theo lộ trình.'
                : 'Gợi ý được điều chỉnh theo bài đã hoàn thành, điểm quiz gần đây và lịch ôn SRS.',
            'avg_quiz_percent' => $avgQuiz,
            'completed_lessons' => $completedLessons,
            'tags' => $tags,
        ];
    }
}
