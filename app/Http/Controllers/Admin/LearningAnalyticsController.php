<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FavoriteItem;
use App\Models\FlashcardCardState;
use App\Models\MinnaQuizAttempt;
use App\Models\User;
use App\Models\UserProgress;
use App\Services\AdminLearningAnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningAnalyticsController extends Controller
{
    public function index(Request $request, AdminLearningAnalyticsService $insightService)
    {
        $days = max(7, min((int) $request->query('days', 30), 90));
        $start = Carbon::today()->subDays($days - 1);

        $dailyActive = UserProgress::query()
            ->whereNotNull('last_accessed_at')
            ->where('last_accessed_at', '>=', $start)
            ->selectRaw($this->dateExpr('last_accessed_at').' as d, COUNT(DISTINCT user_id) as users')
            ->groupBy('d')
            ->pluck('users', 'd')
            ->all();

        $labels = [];
        $activeData = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d/m');
            $activeData[] = (int) ($dailyActive[$date->toDateString()] ?? 0);
        }

        $weeklyActive = UserProgress::query()
            ->where('last_accessed_at', '>=', Carbon::today()->subDays(6))
            ->distinct('user_id')
            ->count('user_id');

        $streakRiskUsers = User::query()
            ->where('role', 'user')
            ->where('current_streak', '>', 0)
            ->whereDate('last_study_date', Carbon::yesterday())
            ->orderByDesc('current_streak')
            ->take(20)
            ->get(['id', 'name', 'email', 'current_streak', 'last_study_date']);

        $lessonCompletions = UserProgress::query()
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->with('lesson:id,number,title')
            ->selectRaw('lesson_id, COUNT(*) as completed_count')
            ->groupBy('lesson_id')
            ->orderByDesc('completed_count')
            ->take(12)
            ->get();

        $weakQuizLessons = MinnaQuizAttempt::query()
            ->with('lesson:id,number,title')
            ->selectRaw('minna_lesson_id, COUNT(*) as attempts, AVG(percent) as avg_percent')
            ->groupBy('minna_lesson_id')
            ->havingRaw('COUNT(*) > 0')
            ->orderBy('avg_percent')
            ->take(12)
            ->get();

        $topFavorites = FavoriteItem::query()
            ->selectRaw('front, back, COUNT(*) as saves')
            ->groupBy('front', 'back')
            ->orderByDesc('saves')
            ->take(12)
            ->get();

        $flashcardReviewsToday = FlashcardCardState::query()
            ->whereDate('last_reviewed_at', Carbon::today())
            ->count();
        $segmentSnapshot = $insightService->segmentSnapshot();

        return view('admin.analytics.index', [
            'days' => $days,
            'labels' => $labels,
            'activeData' => $activeData,
            'weeklyActive' => $weeklyActive,
            'streakRiskUsers' => $streakRiskUsers,
            'retentionSummary' => $insightService->retentionSummary(),
            'cohortRows' => $insightService->cohortRows(),
            'onboardingFunnel' => $insightService->onboardingFunnel(),
            'atRiskUsers' => $insightService->atRiskUsers(),
            'dropOffLessons' => $insightService->dropOffLessons(),
            'dropOffSections' => $insightService->dropOffSections(),
            'contentQuality' => $insightService->contentQuality(),
            'contentSuggestions' => $insightService->contentSuggestions(),
            'segmentDefinitions' => $segmentSnapshot['definitions'],
            'segmentCounts' => $segmentSnapshot['counts'],
            'lessonCompletions' => $lessonCompletions,
            'weakQuizLessons' => $weakQuizLessons,
            'topFavorites' => $topFavorites,
            'flashcardReviewsToday' => $flashcardReviewsToday,
        ]);
    }

    private function dateExpr(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'mysql', 'mariadb' => "DATE({$column})",
            'sqlite' => "date({$column})",
            'pgsql' => "({$column})::date",
            default => "DATE({$column})",
        };
    }
}
