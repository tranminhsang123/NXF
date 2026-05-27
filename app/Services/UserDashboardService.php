<?php

namespace App\Services;

use App\Models\FlashcardCardState;
use App\Models\Kanji;
use App\Models\MinnaLesson;
use App\Models\User;
use App\Models\UserProgress;
use App\Support\OnboardingOptions;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class UserDashboardService
{
    public function __construct(
        private FlashcardService $flashcardService,
        private PersonalizedRoadmapService $roadmapService,
        private StatisticsService $statisticsService,
        private LearningReasonContentService $learningReasonContentService,
        private WeakLessonSuggestionService $weakLessonSuggestionService,
        private WeeklyGoalService $weeklyGoalService,
    ) {}

    private const DASHBOARD_CACHE_TTL = 600; // 10 phút

    public function getDashboardData(User $user): array
    {
        $user->refresh();

        $totalMinnaLessons = Cache::remember(
            'dashboard:total_minna_lessons',
            self::DASHBOARD_CACHE_TTL,
            fn () => MinnaLesson::query()->published()->count()
        );

        $totalKanjis = Cache::remember(
            'dashboard:total_kanjis',
            self::DASHBOARD_CACHE_TTL,
            fn () => Kanji::query()->published()->count()
        );

        $firstMinnaLesson = Cache::remember(
            'dashboard:first_minna_lesson',
            self::DASHBOARD_CACHE_TTL,
            fn () => MinnaLesson::query()->published()->orderBy('number')->first()
        );

        $minnaProgresses = UserProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->with('lesson:id,number,title')
            ->get();

        $completedMinnaLessons = $minnaProgresses
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->count();

        $inProgressMinnaLessons = $minnaProgresses
            ->where('status', UserProgress::STATUS_IN_PROGRESS)
            ->count();

        $latestMinnaProgress = $minnaProgresses
            ->whereNotNull('last_accessed_at')
            ->sortByDesc('last_accessed_at')
            ->first();

        $resumeMinnaLesson = $latestMinnaProgress?->lesson;
        $latestMinnaAccessAt = $latestMinnaProgress?->last_accessed_at;

        $dailyGoalTargetMinna = max(1, (int) ($user->daily_goal_minna_lessons ?? 1));
        $dailyGoalTargetFlash = max(1, (int) ($user->daily_goal_flashcards ?? 12));
        $today = Carbon::today()->toDateString();

        $todayCompletedMinnaLessons = $minnaProgresses
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->filter(
                fn ($p) => $p->completed_at && $p->completed_at->toDateString() === $today
            )
            ->count();

        $todayFlashcardReviews = FlashcardCardState::query()
            ->where('user_id', $user->id)
            ->whereDate('last_reviewed_at', $today)
            ->count();

        $minnaDailyPercent = min(
            100,
            (int) round(($todayCompletedMinnaLessons / $dailyGoalTargetMinna) * 100)
        );
        $flashDailyPercent = min(
            100,
            (int) round(($todayFlashcardReviews / $dailyGoalTargetFlash) * 100)
        );

        $minnaDailyOk = $todayCompletedMinnaLessons >= $dailyGoalTargetMinna;
        $flashDailyOk = $todayFlashcardReviews >= $dailyGoalTargetFlash;
        $isDailyGoalCompleted = $minnaDailyOk || $flashDailyOk;
        $dailyGoalPercent = (int) round(max($minnaDailyPercent, $flashDailyPercent));
        $remainingDailyLessons = max(0, $dailyGoalTargetMinna - $todayCompletedMinnaLessons);
        $remainingDailyFlashcards = max(0, $dailyGoalTargetFlash - $todayFlashcardReviews);

        $minnaProgressPercent = $totalMinnaLessons > 0
            ? round(($completedMinnaLessons / $totalMinnaLessons) * 100)
            : 0;

        $completedLessonIds = $minnaProgresses
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->pluck('lesson_id')
            ->filter()
            ->values()
            ->all();

        $nextMinnaLesson = MinnaLesson::query()
            ->published()
            ->when(! empty($completedLessonIds), fn ($q) => $q->whereNotIn('id', $completedLessonIds))
            ->orderBy('number')
            ->first();

        $primaryMinnaLesson = $resumeMinnaLesson ?: $nextMinnaLesson ?: $firstMinnaLesson;

        $reviewLessonNumbers = $minnaProgresses
            ->filter(fn ($progress) => $progress->lesson !== null)
            ->sortByDesc(fn ($progress) => $progress->last_accessed_at?->timestamp ?? 0)
            ->pluck('lesson.number')
            ->filter()
            ->unique()
            ->take(5)
            ->values()
            ->all();

        if (empty($reviewLessonNumbers) && $primaryMinnaLesson) {
            $reviewLessonNumbers = [(int) $primaryMinnaLesson->number];
        }

        $srsStats = empty($reviewLessonNumbers)
            ? ['due_count' => 0, 'new_count' => 0, 'total_in_scope' => 0]
            : $this->flashcardService->getSrsSummary($user, $reviewLessonNumbers);

        $currentStreak = (int) ($user->current_streak ?? 0);
        $longestStreak = (int) ($user->longest_streak ?? 0);
        $streakAtRisk = $currentStreak > 0
            && $user->last_study_date
            && Carbon::parse($user->last_study_date)->isYesterday();

        $user->loadMissing('badges');
        $roadmap = $this->roadmapService->build($user);
        $reasonFocus = $this->learningReasonContentService->profileFor($user);
        $weakSuggestions = $this->weakLessonSuggestionService->suggest($user);
        $todayFocus = $this->buildTodayFocus(
            $user,
            $roadmap,
            $reasonFocus,
            $weakSuggestions,
            $primaryMinnaLesson,
            $isDailyGoalCompleted,
            $completedMinnaLessons
        );
        $advancedDashboard = $this->buildAdvancedDashboard(
            $user,
            $totalMinnaLessons,
            $completedMinnaLessons,
            $dailyGoalTargetMinna
        );
        $weeklyGoal = $this->weeklyGoalService->build($user);

        return [
            'user' => $user,
            'completedMinnaLessons' => $completedMinnaLessons,
            'inProgressMinnaLessons' => $inProgressMinnaLessons,
            'minnaProgressPercent' => $minnaProgressPercent,
            'totalMinnaLessons' => $totalMinnaLessons,
            'totalKanjis' => $totalKanjis,
            'currentStreak' => $currentStreak,
            'longestStreak' => $longestStreak,
            'streakAtRisk' => $streakAtRisk,
            'firstMinnaLesson' => $firstMinnaLesson,
            'resumeMinnaLesson' => $resumeMinnaLesson,
            'latestMinnaAccessAt' => $latestMinnaAccessAt,
            'dailyGoalTarget' => $dailyGoalTargetMinna,
            'dailyGoalTargetMinna' => $dailyGoalTargetMinna,
            'dailyGoalTargetFlash' => $dailyGoalTargetFlash,
            'todayCompletedMinnaLessons' => $todayCompletedMinnaLessons,
            'todayFlashcardReviews' => $todayFlashcardReviews,
            'minnaDailyPercent' => $minnaDailyPercent,
            'flashDailyPercent' => $flashDailyPercent,
            'dailyGoalPercent' => $dailyGoalPercent,
            'isDailyGoalCompleted' => $isDailyGoalCompleted,
            'minnaDailyOk' => $minnaDailyOk,
            'flashDailyOk' => $flashDailyOk,
            'remainingDailyLessons' => $remainingDailyLessons,
            'remainingDailyFlashcards' => $remainingDailyFlashcards,
            'gamification' => [
                'xp_total' => (int) ($user->xp_total ?? 0),
                'level' => $user->gamificationLevel(),
                'badges' => $user->badges->map(fn ($b) => [
                    'slug' => $b->slug,
                    'name' => $b->name,
                    'icon' => $b->icon,
                    'earned_at' => $b->pivot->earned_at,
                ])->values()->all(),
            ],
            'roadmap' => $roadmap,
            'onboarding' => OnboardingOptions::summaryFor($user),
            'todayFocus' => $todayFocus,
            'reasonFocus' => $reasonFocus,
            'weakSuggestions' => $weakSuggestions,
            'advancedDashboard' => $advancedDashboard,
            'weeklyGoal' => $weeklyGoal,
            'learningPlan' => [
                'daily_goal' => [
                    'target_lessons' => $dailyGoalTargetMinna,
                    'completed_lessons' => $todayCompletedMinnaLessons,
                    'remaining_lessons' => $remainingDailyLessons,
                    'percent_minna' => $minnaDailyPercent,
                    'target_flashcards' => $dailyGoalTargetFlash,
                    'completed_flashcards' => $todayFlashcardReviews,
                    'remaining_flashcards' => $remainingDailyFlashcards,
                    'percent_flash' => $flashDailyPercent,
                    'percent' => $dailyGoalPercent,
                    'completed' => $isDailyGoalCompleted,
                ],
                'resume_lesson' => $this->formatLesson($resumeMinnaLesson),
                'next_lesson' => $this->formatLesson($nextMinnaLesson),
                'review_lesson_numbers' => $reviewLessonNumbers,
                'srs' => $srsStats,
                'tasks' => $this->buildTodayTasks(
                    $primaryMinnaLesson,
                    $reviewLessonNumbers,
                    $srsStats,
                    $isDailyGoalCompleted,
                    $remainingDailyLessons,
                    $remainingDailyFlashcards,
                    $minnaDailyOk
                ),
            ],
        ];
    }

    private function buildTodayFocus(
        User $user,
        array $roadmap,
        array $reasonFocus,
        array $weakSuggestions,
        ?MinnaLesson $primaryMinnaLesson,
        bool $isDailyGoalCompleted,
        int $completedMinnaLessons
    ): array {
        $nextSection = $roadmap['next_section'] ?? null;
        $defaultUrl = $nextSection
            ? route('minna.section', [
                'number' => $nextSection['lesson_number'] ?? 1,
                'sectionKey' => $nextSection['section_key'] ?? '',
            ])
            : ($primaryMinnaLesson
                ? route('minna.show', ['number' => $primaryMinnaLesson->number])
                : route('minna.index'));

        if (! empty($weakSuggestions['inactive_review'])) {
            $review = $weakSuggestions['inactive_review'];

            return [
                'type' => 'short_review',
                'badge' => 'Bài ôn 5 phút',
                'title' => $review['title'],
                'subtitle' => $review['message'],
                'primary_label' => 'Ôn lại ngay',
                'primary_url' => $review['url'],
                'secondary_label' => 'Xem lộ trình',
                'secondary_url' => route('user.progress'),
                'steps' => ['Mở SRS', 'Ôn thẻ đến hạn', 'Kết thúc khi đạt lại nhịp học'],
            ];
        }

        if ($completedMinnaLessons === 0 && ! $user->quick_win_completed_at) {
            $miniLesson = $reasonFocus['mini_lesson'] ?? [];

            return [
                'type' => 'new_user_quick_win',
                'badge' => 'Gợi ý 5 phút cho người mới',
                'title' => $miniLesson['title'] ?? 'Bài 5 phút đầu tiên',
                'subtitle' => $reasonFocus['focus_text'] ?? 'Bắt đầu bằng bài ngắn để có cảm giác hoàn thành ngay.',
                'primary_label' => 'Bắt đầu 5 phút',
                'primary_url' => $defaultUrl,
                'secondary_label' => 'Xem kết quả placement',
                'secondary_url' => route('onboarding.result'),
                'steps' => $miniLesson['steps'] ?? ['Mở bài ngắn', 'Nghe và đọc to', 'Hoàn thành một phần'],
            ];
        }

        if (! empty($weakSuggestions['weak_quiz'])) {
            $weakLesson = $weakSuggestions['weak_quiz'][0];

            return [
                'type' => 'quiz_review',
                'badge' => 'Ôn theo lỗi quiz',
                'title' => 'Ôn lại bài '.$weakLesson['lesson_number'].' vì quiz mới đạt '.$weakLesson['percent'].'%',
                'subtitle' => 'Hệ thống ưu tiên bài có điểm thấp trước khi đẩy bài mới.',
                'primary_label' => 'Ôn bài yếu',
                'primary_url' => $weakLesson['url'],
                'secondary_label' => 'Ôn SRS',
                'secondary_url' => route('flashcard.index'),
                'steps' => ['Xem lại từ sai', 'Làm lại quiz', 'Lưu từ cần nhớ vào flashcard'],
            ];
        }

        if ($isDailyGoalCompleted) {
            return [
                'type' => 'stretch',
                'badge' => 'Đã xong mục tiêu ngày',
                'title' => 'Bạn có thể ôn nhẹ thêm hoặc nghỉ đúng nhịp',
                'subtitle' => 'Mục tiêu hôm nay đã đạt, phần gợi ý tiếp theo dành cho học thêm tự chọn.',
                'primary_label' => 'Ôn flashcard',
                'primary_url' => route('flashcard.index'),
                'secondary_label' => 'Xem bảng xếp hạng',
                'secondary_url' => route('leaderboard.index'),
                'steps' => ['Ôn 5 thẻ khó', 'Xem lại tiến độ', 'Giữ năng lượng cho ngày mai'],
            ];
        }

        return [
            'type' => 'recommended',
            'badge' => 'Hôm nay học gì?',
            'title' => $roadmap['headline'] ?? 'Tiếp tục lộ trình Minna',
            'subtitle' => $roadmap['reason'] ?? 'Gợi ý dựa trên tiến độ học thật của bạn.',
            'primary_label' => 'Học bài này',
            'primary_url' => $defaultUrl,
            'secondary_label' => 'Ôn SRS',
            'secondary_url' => route('flashcard.index'),
            'steps' => ['Học phần được gợi ý', 'Nghe audio nếu có', 'Hoàn thành quiz hoặc flashcard'],
        ];
    }

    private function formatLesson(?MinnaLesson $lesson): ?array
    {
        if (! $lesson) {
            return null;
        }

        return [
            'id' => $lesson->id,
            'number' => $lesson->number,
            'title' => $lesson->title,
            'description' => $lesson->description,
        ];
    }

    private function buildTodayTasks(
        ?MinnaLesson $primaryMinnaLesson,
        array $reviewLessonNumbers,
        array $srsStats,
        bool $isDailyGoalCompleted,
        int $remainingDailyLessons,
        int $remainingDailyFlashcards,
        bool $minnaDailyOk
    ): array {
        $tasks = [];

        if ($primaryMinnaLesson) {
            $tasks[] = [
                'id' => 'continue_minna',
                'type' => 'lesson',
                'title' => 'Tiếp tục bài '.$primaryMinnaLesson->number,
                'subtitle' => $primaryMinnaLesson->title,
                'done' => $minnaDailyOk,
                'target' => [
                    'screen' => 'LessonDetail',
                    'lesson_number' => $primaryMinnaLesson->number,
                ],
            ];
        }

        $dueCount = (int) ($srsStats['due_count'] ?? 0);
        $newCount = (int) ($srsStats['new_count'] ?? 0);
        $reviewTitle = $dueCount > 0
            ? 'Ôn '.$dueCount.' thẻ đến hạn'
            : 'Học '.$newCount.' thẻ mới';

        $tasks[] = [
            'id' => 'review_flashcards',
            'type' => 'flashcard',
            'title' => $reviewTitle,
            'subtitle' => empty($reviewLessonNumbers)
                ? 'Chưa có phạm vi ôn tập'
                : 'Phạm vi bài '.implode(', ', $reviewLessonNumbers),
            'done' => $dueCount === 0 && $newCount === 0,
            'target' => [
                'screen' => 'Flashcards',
                'lesson_numbers' => $reviewLessonNumbers,
                'mode' => 'srs',
            ],
        ];

        $tasks[] = [
            'id' => 'daily_goal',
            'type' => 'daily_goal',
            'title' => $isDailyGoalCompleted
                ? 'Mục tiêu ngày đã xong'
                : 'Còn '.$remainingDailyLessons.' bài Minna hoặc '.$remainingDailyFlashcards.' thẻ SRS',
            'subtitle' => 'Đạt một trong hai mục tiêu: hoàn thành bài Minna hôm nay hoặc ôn đủ số thẻ mục tiêu.',
            'done' => $isDailyGoalCompleted,
            'target' => [
                'screen' => 'Progress',
            ],
        ];

        return $tasks;
    }

    private function buildAdvancedDashboard(
        User $user,
        int $totalMinnaLessons,
        int $completedMinnaLessons,
        int $dailyGoalTargetMinna
    ): array {
        $lessonsByDay = $this->statisticsService->getLessonsCompletedByDay($user, 14);
        $lessonsByWeek = $this->statisticsService->getLessonsCompletedByWeek($user, 8);
        $weeklyData = array_map('intval', $lessonsByWeek['data'] ?? []);
        $recentWeeks = array_slice($weeklyData, -4);
        $avgLessonsPerWeek = count($recentWeeks) > 0
            ? round(array_sum($recentWeeks) / count($recentWeeks), 2)
            : 0.0;
        $remainingLessons = max(0, $totalMinnaLessons - $completedMinnaLessons);

        $forecast = [
            'remaining_lessons' => $remainingLessons,
            'avg_lessons_per_week' => $avgLessonsPerWeek,
            'estimated_completion_date' => null,
            'weeks_remaining' => null,
            'confidence' => 'low',
            'message' => 'Chưa đủ dữ liệu tiến độ để dự báo. Hoàn thành thêm vài bài để dashboard tính chính xác hơn.',
        ];

        if ($remainingLessons === 0 && $totalMinnaLessons > 0) {
            $forecast['confidence'] = 'high';
            $forecast['message'] = 'Bạn đã hoàn thành toàn bộ lộ trình Minna hiện có.';
        } elseif ($avgLessonsPerWeek > 0) {
            $weeksRemaining = (int) ceil($remainingLessons / $avgLessonsPerWeek);
            $estimatedDate = Carbon::today()->addWeeks($weeksRemaining);

            $forecast['estimated_completion_date'] = $estimatedDate->toDateString();
            $forecast['weeks_remaining'] = $weeksRemaining;
            $forecast['confidence'] = $avgLessonsPerWeek >= 2 ? 'high' : 'medium';
            $forecast['message'] = 'Nếu giữ tốc độ hiện tại, bạn có thể hoàn thành vào '.$estimatedDate->format('d/m/Y').'.';
        }

        return [
            'charts' => [
                'lessons_by_day' => $lessonsByDay,
                'lessons_by_week' => $lessonsByWeek,
            ],
            'forecast' => $forecast,
            'daily_goal_lessons' => $dailyGoalTargetMinna,
        ];
    }
}
