<?php

namespace App\Services;

use App\Models\ChatGroupMember;
use App\Models\ContentErrorReport;
use App\Models\FavoriteItem;
use App\Models\GrowthCampaignRecipient;
use App\Models\MinnaLesson;
use App\Models\MinnaQuizAttempt;
use App\Models\Notification;
use App\Models\User;
use App\Models\UserMinnaSectionProgress;
use App\Models\UserProgress;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AdminLearningAnalyticsService
{
    public function __construct(
        private AdminAudienceSegmentService $segmentService,
        private ContentPublishQualityService $qualityService
    ) {}

    public function retentionSummary(): array
    {
        $users = User::query()
            ->where('role', 'user')
            ->get(['id', 'created_at', 'last_study_date', 'current_streak']);
        $latestActivity = $this->latestActivityMap();

        $retentionFor = function (int $days) use ($users, $latestActivity): array {
            $eligible = $users->filter(fn (User $user) => $user->created_at?->lte(now()->subDays($days)));
            $returned = $eligible->filter(function (User $user) use ($days, $latestActivity) {
                $activityAt = $latestActivity[$user->id] ?? null;

                return $activityAt && $activityAt->gte($user->created_at->copy()->addDays($days));
            });

            return [
                'eligible' => $eligible->count(),
                'returned' => $returned->count(),
                'rate' => $eligible->count() > 0 ? round($returned->count() / $eligible->count() * 100, 1) : 0,
            ];
        };

        return [
            'd1' => $retentionFor(1),
            'd7' => $retentionFor(7),
            'd30' => $retentionFor(30),
            'dau' => $this->activeUserCount(Carbon::today()),
            'wau' => $this->activeUserCount(Carbon::today()->subDays(6)),
            'average_streak' => round((float) $users->avg('current_streak'), 1),
        ];
    }

    public function cohortRows(int $weeks = 8): array
    {
        $latestActivity = $this->latestActivityMap();
        $rows = [];

        for ($i = $weeks - 1; $i >= 0; $i--) {
            $start = Carbon::today()->startOfWeek()->subWeeks($i);
            $end = $start->copy()->endOfWeek();
            $users = User::query()
                ->where('role', 'user')
                ->whereBetween('created_at', [$start, $end])
                ->get(['id', 'created_at']);

            $rates = [];
            foreach ([1, 2, 4, 8] as $week) {
                if ($start->copy()->addWeeks($week)->isFuture()) {
                    $rates[$week] = null;
                    continue;
                }

                $kept = $users->filter(function (User $user) use ($week, $latestActivity) {
                    $activityAt = $latestActivity[$user->id] ?? null;

                    return $activityAt && $activityAt->gte($user->created_at->copy()->addWeeks($week));
                })->count();

                $rates[$week] = $users->count() > 0 ? round($kept / $users->count() * 100, 1) : 0;
            }

            $rows[] = [
                'label' => $start->format('d/m').' - '.$end->format('d/m'),
                'users' => $users->count(),
                'rates' => $rates,
            ];
        }

        return $rows;
    }

    public function onboardingFunnel(int $days = 30): array
    {
        $users = User::query()
            ->where('role', 'user')
            ->where('created_at', '>=', Carbon::today()->subDays($days - 1))
            ->get(['id', 'created_at', 'onboarding_completed_at']);
        $base = max(1, $users->count());
        $userIds = $users->pluck('id');

        $startedIds = UserProgress::query()
            ->whereIn('user_id', $userIds)
            ->pluck('user_id')
            ->unique();
        $completedIds = UserProgress::query()
            ->whereIn('user_id', $userIds)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->pluck('user_id')
            ->unique();

        $started24h = $users->filter(function (User $user) {
            $firstProgressAt = UserProgress::query()
                ->where('user_id', $user->id)
                ->whereNotNull('last_accessed_at')
                ->min('last_accessed_at');

            return $firstProgressAt && Carbon::parse($firstProgressAt)->lte($user->created_at->copy()->addDay());
        })->count();

        $steps = [
            ['label' => 'Đăng ký', 'count' => $users->count()],
            ['label' => 'Hoàn thành onboarding', 'count' => $users->whereNotNull('onboarding_completed_at')->count()],
            ['label' => 'Bắt đầu bài đầu tiên', 'count' => $startedIds->count()],
            ['label' => 'Học trong 24h đầu', 'count' => $started24h],
            ['label' => 'Hoàn thành bài đầu tiên', 'count' => $completedIds->count()],
        ];

        return collect($steps)
            ->map(fn (array $step) => $step + ['rate' => round($step['count'] / $base * 100, 1)])
            ->all();
    }

    public function atRiskUsers(int $limit = 20): Collection
    {
        return $this->segmentService->query('at_risk_5_10')
            ->withCount(['progresses', 'minnaQuizAttempts'])
            ->orderByDesc('current_streak')
            ->orderByDesc('last_study_date')
            ->take($limit)
            ->get(['id', 'name', 'email', 'current_streak', 'last_study_date', 'xp_total']);
    }

    public function dropOffLessons(int $limit = 12): Collection
    {
        return UserProgress::query()
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->whereNotNull('lesson_id')
            ->with('lesson:id,number,title')
            ->selectRaw("lesson_id, COUNT(DISTINCT user_id) as started_count, COUNT(DISTINCT CASE WHEN status = 'completed' THEN user_id END) as completed_count")
            ->groupBy('lesson_id')
            ->get()
            ->map(function ($row) {
                $row->drop_count = max(0, (int) $row->started_count - (int) $row->completed_count);
                $row->drop_rate = (int) $row->started_count > 0
                    ? round($row->drop_count / (int) $row->started_count * 100, 1)
                    : 0;

                return $row;
            })
            ->sortByDesc('drop_rate')
            ->take($limit)
            ->values();
    }

    public function dropOffSections(int $limit = 12): Collection
    {
        return UserMinnaSectionProgress::query()
            ->with(['lesson:id,number,title', 'section:id,title,key'])
            ->selectRaw("minna_lesson_id, minna_section_id, section_key, COUNT(DISTINCT user_id) as started_count, COUNT(DISTINCT CASE WHEN status = 'completed' THEN user_id END) as completed_count")
            ->groupBy('minna_lesson_id', 'minna_section_id', 'section_key')
            ->get()
            ->map(function ($row) {
                $row->drop_count = max(0, (int) $row->started_count - (int) $row->completed_count);
                $row->drop_rate = (int) $row->started_count > 0
                    ? round($row->drop_count / (int) $row->started_count * 100, 1)
                    : 0;

                return $row;
            })
            ->sortByDesc('drop_rate')
            ->take($limit)
            ->values();
    }

    public function contentQuality(): array
    {
        $openReportCount = ContentErrorReport::query()
            ->whereIn('status', [ContentErrorReport::STATUS_PENDING, ContentErrorReport::STATUS_IN_PROGRESS])
            ->count();

        $missingAudioLessons = MinnaLesson::query()
            ->with('sections')
            ->orderBy('number')
            ->get()
            ->map(function (MinnaLesson $lesson) {
                $audioItem = collect($this->qualityService->checklist($lesson)['items'])
                    ->firstWhere('key', 'minna_audio');

                return [
                    'lesson' => $lesson,
                    'missing' => (int) data_get($audioItem, 'meta.missing_audio', 0),
                    'required' => (int) data_get($audioItem, 'meta.required_audio', 0),
                ];
            })
            ->filter(fn (array $row) => $row['missing'] > 0)
            ->sortByDesc('missing')
            ->take(8)
            ->values();

        $highWrongQuizLessons = MinnaQuizAttempt::query()
            ->with('lesson:id,number,title')
            ->selectRaw('minna_lesson_id, COUNT(*) as attempts, AVG(percent) as avg_percent')
            ->groupBy('minna_lesson_id')
            ->havingRaw('AVG(percent) < 20')
            ->orderBy('avg_percent')
            ->take(8)
            ->get();

        $activeLessonIds = UserProgress::query()
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->where('last_accessed_at', '>=', Carbon::today()->subDays(30))
            ->pluck('lesson_id')
            ->filter()
            ->unique();

        $idleLessons = MinnaLesson::query()
            ->whereNotIn('id', $activeLessonIds)
            ->orderBy('number')
            ->take(8)
            ->get(['id', 'number', 'title']);

        return [
            'open_report_count' => $openReportCount,
            'missing_audio_lessons' => $missingAudioLessons,
            'high_wrong_quiz_lessons' => $highWrongQuizLessons,
            'idle_lessons' => $idleLessons,
        ];
    }

    public function contentSuggestions(int $limit = 10): Collection
    {
        $favorites = FavoriteItem::query()
            ->selectRaw('front, back, COUNT(*) as saves')
            ->groupBy('front', 'back')
            ->orderByDesc('saves')
            ->take($limit)
            ->get();

        return $favorites->map(fn ($row) => [
            'title' => $row->front,
            'subtitle' => $row->back,
            'score' => (int) $row->saves,
            'reason' => 'Được lưu nhiều nhưng nên kiểm tra đã có ví dụ/câu luyện tập đủ chưa.',
        ]);
    }

    public function segmentSnapshot(): array
    {
        return [
            'definitions' => $this->segmentService->definitions(),
            'counts' => $this->segmentService->counts(),
        ];
    }

    public function userLearningProfile(User $user): array
    {
        $latestProgressAt = UserProgress::query()
            ->where('user_id', $user->id)
            ->max('last_accessed_at');

        $completedLessons = UserProgress::query()
            ->where('user_id', $user->id)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->with('lesson:id,number,title')
            ->latest('completed_at')
            ->take(6)
            ->get();

        $quizStats = MinnaQuizAttempt::query()
            ->where('user_id', $user->id)
            ->selectRaw('COUNT(*) as attempts, AVG(percent) as avg_percent, MAX(percent) as best_percent')
            ->first();

        $recentQuizAttempts = MinnaQuizAttempt::query()
            ->where('user_id', $user->id)
            ->with('lesson:id,number,title')
            ->latest('completed_at')
            ->take(6)
            ->get();

        $campaignCount = class_exists(GrowthCampaignRecipient::class)
            ? GrowthCampaignRecipient::query()->where('user_id', $user->id)->count()
            : Notification::query()->where('user_id', $user->id)->where('type', 'growth_campaign')->count();

        return [
            'last_activity_at' => $latestProgressAt ? Carbon::parse($latestProgressAt) : $user->last_study_date,
            'completed_lessons_count' => UserProgress::query()
                ->where('user_id', $user->id)
                ->where('status', UserProgress::STATUS_COMPLETED)
                ->count(),
            'in_progress_lessons_count' => UserProgress::query()
                ->where('user_id', $user->id)
                ->where('status', UserProgress::STATUS_IN_PROGRESS)
                ->count(),
            'favorite_count' => FavoriteItem::query()->where('user_id', $user->id)->count(),
            'group_count' => ChatGroupMember::query()->where('user_id', $user->id)->count(),
            'campaign_count' => $campaignCount,
            'quiz_attempts' => (int) ($quizStats->attempts ?? 0),
            'quiz_average' => $quizStats->avg_percent !== null ? round((float) $quizStats->avg_percent, 1) : null,
            'quiz_best' => $quizStats->best_percent !== null ? (int) $quizStats->best_percent : null,
            'completed_lessons' => $completedLessons,
            'recent_quiz_attempts' => $recentQuizAttempts,
        ];
    }

    private function latestActivityMap(): array
    {
        $map = [];

        foreach (User::query()->where('role', 'user')->get(['id', 'last_study_date']) as $user) {
            if ($user->last_study_date) {
                $map[$user->id] = Carbon::parse($user->last_study_date);
            }
        }

        foreach (UserProgress::query()
            ->whereNotNull('last_accessed_at')
            ->selectRaw('user_id, MAX(last_accessed_at) as last_seen')
            ->groupBy('user_id')
            ->get() as $row) {
            $activityAt = Carbon::parse($row->last_seen);
            if (! isset($map[$row->user_id]) || $activityAt->gt($map[$row->user_id])) {
                $map[$row->user_id] = $activityAt;
            }
        }

        return $map;
    }

    private function activeUserCount(Carbon $from): int
    {
        $progressIds = UserProgress::query()
            ->where('last_accessed_at', '>=', $from)
            ->pluck('user_id');
        $studyDateIds = User::query()
            ->where('role', 'user')
            ->whereDate('last_study_date', '>=', $from)
            ->pluck('id');

        return $progressIds->merge($studyDateIds)->unique()->count();
    }
}
