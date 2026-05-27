<?php

namespace App\Services;

use App\Models\FlashcardCardState;
use App\Models\LearningEvent;
use App\Models\MinnaQuizAttempt;
use App\Models\User;
use App\Models\UserProgress;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class WeeklyGoalService
{
    public function build(User $user): array
    {
        $now = Carbon::now();
        $start = $now->copy()->startOfWeek(Carbon::MONDAY);
        $end = $now->copy()->endOfWeek(Carbon::SUNDAY);
        $targets = $this->targetsFor($user);

        $completedLessons = UserProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->whereBetween('completed_at', [$start, $end])
            ->count();

        $flashcardReviewEvents = LearningEvent::query()
            ->where('user_id', $user->id)
            ->where('event_type', LearningEvent::FLASHCARD_REVIEWED)
            ->whereBetween('occurred_at', [$start, $end])
            ->count();

        $flashcardReviewedCards = FlashcardCardState::query()
            ->where('user_id', $user->id)
            ->whereBetween('last_reviewed_at', [$start, $end])
            ->count();

        $completedFlashcards = max($flashcardReviewEvents, $flashcardReviewedCards);

        $quizEvents = LearningEvent::query()
            ->where('user_id', $user->id)
            ->whereIn('event_type', [LearningEvent::QUIZ_SUBMITTED, LearningEvent::ADVANCED_QUIZ_SUBMITTED])
            ->whereBetween('occurred_at', [$start, $end])
            ->count();

        $minnaQuizAttempts = MinnaQuizAttempt::query()
            ->where('user_id', $user->id)
            ->whereBetween('completed_at', [$start, $end])
            ->count();

        $completedQuizzes = max($quizEvents, $minnaQuizAttempts);
        $studyDays = $this->studyDays($user, $start, $end);

        $metrics = [
            'lessons' => $this->metric('Học bài', $completedLessons, $targets['lessons'], 'bài', route('minna.index')),
            'flashcards' => $this->metric('Ôn flashcard', $completedFlashcards, $targets['flashcards'], 'thẻ', route('flashcard.index')),
            'quizzes' => $this->metric('Làm quiz', $completedQuizzes, $targets['quizzes'], 'quiz', route('minna.index')),
            'streak_days' => $this->metric('Giữ streak', $studyDays->count(), $targets['streak_days'], 'ngày', route('user.activity')),
        ];

        $percent = (int) round(collect($metrics)->avg('percent'));
        $remaining = collect($metrics)
            ->mapWithKeys(fn (array $metric, string $key) => [$key => max(0, $metric['target'] - $metric['completed'])])
            ->all();

        return [
            'week_start' => $start->toDateString(),
            'week_end' => $end->toDateString(),
            'week_label' => $start->format('d/m').' - '.$end->format('d/m'),
            'percent' => min(100, $percent),
            'status' => $this->statusFor($percent),
            'metrics' => $metrics,
            'remaining' => $remaining,
            'study_days' => $studyDays->values()->all(),
            'summary' => $this->summary($percent, $metrics, $remaining, $now),
            'next_week_plan' => $this->nextWeekPlan($targets, $metrics, $remaining),
        ];
    }

    private function targetsFor(User $user): array
    {
        $dailyLessons = max(1, (int) ($user->daily_goal_minna_lessons ?? 1));
        $dailyFlashcards = max(8, (int) ($user->daily_goal_flashcards ?? 12));
        $dailyMinutes = max(5, (int) ($user->daily_study_minutes ?? 20));

        $targetLessons = max(2, min(7, $dailyLessons * 3));
        $targetFlashcards = max(30, min(250, $dailyFlashcards * 5));
        $targetQuizzes = max(3, min(10, $targetLessons + 1));
        $targetStreakDays = match (true) {
            $dailyMinutes >= 45 => 6,
            $dailyMinutes <= 10 => 4,
            default => 5,
        };

        return [
            'lessons' => $targetLessons,
            'flashcards' => $targetFlashcards,
            'quizzes' => $targetQuizzes,
            'streak_days' => $targetStreakDays,
        ];
    }

    private function metric(string $label, int $completed, int $target, string $unit, string $url): array
    {
        $percent = $target > 0 ? (int) round(($completed / $target) * 100) : 0;

        return [
            'label' => $label,
            'completed' => $completed,
            'target' => $target,
            'remaining' => max(0, $target - $completed),
            'unit' => $unit,
            'percent' => min(100, $percent),
            'url' => $url,
            'done' => $completed >= $target,
        ];
    }

    private function studyDays(User $user, Carbon $start, Carbon $end): Collection
    {
        $eventDays = LearningEvent::query()
            ->where('user_id', $user->id)
            ->whereBetween('occurred_at', [$start, $end])
            ->pluck('occurred_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $lessonDays = UserProgress::query()
            ->where('user_id', $user->id)
            ->whereBetween('completed_at', [$start, $end])
            ->pluck('completed_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $quizDays = MinnaQuizAttempt::query()
            ->where('user_id', $user->id)
            ->whereBetween('completed_at', [$start, $end])
            ->pluck('completed_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        $flashcardDays = FlashcardCardState::query()
            ->where('user_id', $user->id)
            ->whereBetween('last_reviewed_at', [$start, $end])
            ->pluck('last_reviewed_at')
            ->filter()
            ->map(fn ($date) => Carbon::parse($date)->toDateString());

        return $eventDays
            ->merge($lessonDays)
            ->merge($quizDays)
            ->merge($flashcardDays)
            ->unique()
            ->sort()
            ->values();
    }

    private function summary(int $percent, array $metrics, array $remaining, Carbon $now): array
    {
        $doneLabels = collect($metrics)
            ->filter(fn (array $metric) => $metric['done'])
            ->pluck('label')
            ->values()
            ->all();

        $lowest = collect($metrics)
            ->sortBy('percent')
            ->first();

        $isWeekend = $now->isFriday() || $now->isWeekend();
        $title = $isWeekend ? 'Tổng kết cuối tuần' : 'Tổng kết tuần hiện tại';

        $message = match (true) {
            $percent >= 100 => 'Bạn đã hoàn thành mục tiêu tuần. Phần còn lại có thể dùng để ôn nhẹ hoặc học thêm chủ đề thực tế.',
            $isWeekend && $percent < 70 => 'Tuần này còn thiếu vài mục tiêu quan trọng. Ưu tiên mục thấp nhất trước để cứu retention cuối tuần.',
            $percent >= 70 => 'Tuần này đang đi đúng nhịp. Hoàn thành thêm phần còn thiếu là đủ đẹp.',
            default => 'Tuần này mới khởi động. Chỉ cần một phiên 5-10 phút hôm nay là số tuần sẽ sáng hơn ngay.',
        };

        return [
            'title' => $title,
            'message' => $message,
            'wins' => $doneLabels,
            'focus_label' => $lowest['label'] ?? 'Học bài',
            'focus_remaining' => $lowest['remaining'] ?? 0,
            'focus_unit' => $lowest['unit'] ?? '',
            'is_weekend' => $isWeekend,
            'remaining_total' => array_sum($remaining),
        ];
    }

    private function nextWeekPlan(array $targets, array $metrics, array $remaining): array
    {
        $focus = [];

        if (($remaining['lessons'] ?? 0) > 0) {
            $focus[] = 'Chia '.$targets['lessons'].' bài Minna thành 3 buổi chính.';
        } else {
            $targets['lessons'] = min(8, $targets['lessons'] + 1);
            $focus[] = 'Tăng nhẹ mục tiêu bài học lên '.$targets['lessons'].' bài nếu tuần này còn năng lượng.';
        }

        if (($remaining['flashcards'] ?? 0) > 0) {
            $focus[] = 'Mở mỗi buổi bằng 5-10 phút SRS để không dồn thẻ cuối tuần.';
        }

        if (($remaining['quizzes'] ?? 0) > 0) {
            $focus[] = 'Sau mỗi bài mới, làm ngay 1 quiz để khóa kiến thức.';
        }

        if (($remaining['streak_days'] ?? 0) > 0) {
            $focus[] = 'Chọn trước '.($metrics['streak_days']['target'] ?? 5).' ngày học ngắn để giữ streak.';
        }

        return [
            'targets' => $targets,
            'focus' => array_slice(array_values(array_unique($focus)), 0, 4),
            'primary_url' => ($remaining['flashcards'] ?? 0) > ($remaining['lessons'] ?? 0)
                ? route('flashcard.index')
                : route('minna.index'),
        ];
    }

    private function statusFor(int $percent): string
    {
        return match (true) {
            $percent >= 100 => 'completed',
            $percent >= 70 => 'on_track',
            $percent >= 35 => 'needs_push',
            default => 'starting',
        };
    }
}
