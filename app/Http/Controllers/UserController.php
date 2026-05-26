<?php

namespace App\Http\Controllers;

use App\Models\MinnaSection;
use App\Models\UserMinnaSectionProgress;
use App\Models\UserProgress;
use App\Services\StatisticsService;
use App\Services\UserDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct(
        private StatisticsService $statisticsService,
        private UserDashboardService $dashboardService,
    ) {}

    public function dashboard()
    {
        $user = Auth::user();
        $data = $this->dashboardService->getDashboardData($user);

        return view('user.dashboard', $data);
    }

    public function progress()
    {
        $user = Auth::user();

        $minnaProgresses = UserProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->with('lesson:id,number,title,description')
            ->orderByDesc('last_accessed_at')
            ->get();

        $lessonIds = $minnaProgresses->pluck('lesson_id')->filter()->values()->all();
        $sectionCounts = MinnaSection::query()
            ->published()
            ->whereIn('lesson_id', $lessonIds)
            ->selectRaw('lesson_id, count(*) as total_sections')
            ->groupBy('lesson_id')
            ->pluck('total_sections', 'lesson_id');
        $completedCounts = UserMinnaSectionProgress::query()
            ->where('user_id', $user->id)
            ->whereIn('minna_lesson_id', $lessonIds)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->selectRaw('minna_lesson_id, count(*) as completed_sections')
            ->groupBy('minna_lesson_id')
            ->pluck('completed_sections', 'minna_lesson_id');
        $sectionProgressByLessonId = collect($lessonIds)->mapWithKeys(function ($lessonId) use ($sectionCounts, $completedCounts) {
            $total = (int) ($sectionCounts[$lessonId] ?? 0);
            $completed = (int) ($completedCounts[$lessonId] ?? 0);

            return [$lessonId => [
                'total' => $total,
                'completed' => $completed,
                'percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
            ]];
        });

        return view('user.progress', [
            'user' => $user,
            'minnaProgresses' => $minnaProgresses,
            'sectionProgressByLessonId' => $sectionProgressByLessonId,
        ]);
    }

    /**
     * Thống kê chi tiết: biểu đồ theo ngày, tuần, tổng bài/từ
     */
    public function statistics()
    {
        $user = Auth::user();

        $byDay = $this->statisticsService->getLessonsCompletedByDay($user, 7);
        $byWeek = $this->statisticsService->getLessonsCompletedByWeek($user, 8);
        $summary = $this->statisticsService->getSummary($user);

        return view('user.statistics', [
            'user' => $user,
            'byDay' => $byDay,
            'byWeek' => $byWeek,
            'summary' => $summary,
        ]);
    }

    public function activity()
    {
        $user = Auth::user();

        return view('user.activity', [
            'user' => $user,
            'groups' => $this->statisticsService->getActivityTimeline($user),
        ]);
    }
}
