<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\N5CourseData;
use App\Models\UserProgress;
use App\Services\CourseService;
use App\Services\FlashcardService;
use App\Services\GlobalSearchService;
use App\Services\KanjiService;
use App\Services\PracticalTopicService;
use App\Services\StatisticsService;
use App\Services\UserDashboardService;
use App\Services\UserMistakeService;
use App\Services\WeeklyGoalService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;

class UserLearningController extends Controller
{
    public function dashboard(Request $request, UserDashboardService $dashboardService): JsonResponse
    {
        $data = $dashboardService->getDashboardData($request->user());

        return response()->json($data);
    }

    public function progress(Request $request): JsonResponse
    {
        $perPage = max(1, min((int) $request->query('per_page', 20), 100));

        $progresses = UserProgress::query()
            ->where('user_id', $request->user()->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->with('lesson:id,number,title,description')
            ->orderByDesc('last_accessed_at')
            ->paginate($perPage);

        return response()->json($progresses);
    }

    public function mistakes(Request $request, UserMistakeService $mistakeService): JsonResponse
    {
        return response()->json($mistakeService->build($request->user()));
    }

    public function weeklyGoal(Request $request, WeeklyGoalService $weeklyGoalService): JsonResponse
    {
        return response()->json($weeklyGoalService->build($request->user()));
    }

    public function practicalTopics(Request $request, PracticalTopicService $topicService): JsonResponse
    {
        return response()->json([
            'topics' => $topicService->all(),
            'recommended' => $topicService->recommendedFor($request->user()),
        ]);
    }

    public function practicalTopic(Request $request, PracticalTopicService $topicService, string $slug): JsonResponse
    {
        $topic = $topicService->find($slug);

        if (! $topic) {
            return response()->json(['message' => 'Không tìm thấy chủ đề.'], 404);
        }

        return response()->json(['topic' => $topic]);
    }

    public function statistics(Request $request, StatisticsService $statisticsService): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'by_day' => $statisticsService->getLessonsCompletedByDay($user, 7),
            'by_week' => $statisticsService->getLessonsCompletedByWeek($user, 8),
            'summary' => $statisticsService->getSummary($user),
        ]);
    }

    public function kanjiLevels(KanjiService $kanjiService): JsonResponse
    {
        return response()->json([
            'levels' => KanjiService::LEVELS,
            'counts' => $kanjiService->getCountsByLevel(),
        ]);
    }

    public function kanjiList(Request $request, KanjiService $kanjiService, string $level): JsonResponse
    {
        if (! in_array($level, KanjiService::LEVELS, true)) {
            return response()->json(['message' => 'Level khong hop le.'], 404);
        }

        $perPage = max(1, min((int) $request->query('per_page', 40), 100));
        $search = $request->query('search');

        $query = $kanjiService->getKanjisWithFilters($level, is_string($search) ? $search : null)->published();
        $paginated = $query->paginate($perPage);

        return response()->json($paginated);
    }

    public function vocabularyLessons(FlashcardService $flashcardService): JsonResponse
    {
        return response()->json([
            'lessons' => $flashcardService->getLessonsWithVocabCount(),
        ]);
    }

    public function vocabularyByLesson(int $number): JsonResponse
    {
        $lesson = MinnaLesson::query()->published()->where('number', $number)->first();

        if (! $lesson) {
            return response()->json(['message' => 'Không tìm thấy bài học.'], 404);
        }

        $section = MinnaSection::query()
            ->published()
            ->where('lesson_id', $lesson->id)
            ->where('key', 'tu-vung')
            ->first();

        return response()->json([
            'lesson' => $lesson,
            'content' => $section?->content ?? [],
        ]);
    }

    public function courseLevels(CourseService $courseService): JsonResponse
    {
        $levels = ['N5', 'N4', 'N3', 'N2', 'N1'];
        $courses = [];

        foreach ($levels as $level) {
            $meta = $courseService->getCourseMetadata($level);
            if ($meta) {
                $courses[] = array_merge(['level' => $level], $meta);
            }
        }

        return response()->json(['courses' => $courses]);
    }

    public function courseSections(CourseService $courseService, string $level): JsonResponse
    {
        try {
            $validatedLevel = $courseService->validateLevel($level);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        $meta = $courseService->getCourseMetadata($validatedLevel);
        if (! $meta) {
            return response()->json(['message' => 'Không tìm thấy khóa học.'], 404);
        }

        $sections = $validatedLevel === 'N5' ? $courseService->getN5Sections() : [];

        return response()->json([
            'level' => $validatedLevel,
            'course' => $meta,
            'sections' => $sections,
        ]);
    }

    public function courseSectionItems(CourseService $courseService, string $level, string $sectionType): JsonResponse
    {
        try {
            $validatedLevel = $courseService->validateLevel($level);
            $courseService->validateSectionType($sectionType);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        if ($sectionType === 'luyen_doc') {
            return response()->json(['items' => $courseService->getLuyenDocLessons()]);
        }

        if ($sectionType === 'marugoto_n5') {
            return response()->json(['items' => $courseService->getMarugotoLessons()]);
        }

        if ($sectionType === 'speed_master_n5') {
            return response()->json([
                'level' => $validatedLevel,
                'items' => $courseService->getSpeedMasterLessons(),
            ]);
        }

        return response()->json(['items' => []]);
    }

    public function courseSectionItemDetail(CourseService $courseService, string $level, string $sectionType, string $itemKey): JsonResponse
    {
        try {
            $validatedLevel = $courseService->validateLevel($level);
            $courseService->validateSectionType($sectionType);
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        if ($sectionType === 'luyen_doc') {
            $id = (int) $itemKey;
            $item = $courseService->getLuyenDocDetail($id);

            return response()->json([
                'level' => $validatedLevel,
                'item' => [
                    'id' => $item->id,
                    'bai' => $item->bai,
                    'title' => $item->title,
                    'content' => $item->content,
                    'section_key' => $item->section_key,
                ],
            ]);
        }

        if ($sectionType === 'marugoto_n5') {
            $id = (int) $itemKey;
            $item = $courseService->getMarugotoDetail($id);

            return response()->json([
                'level' => $validatedLevel,
                'item' => [
                    'id' => $item->id,
                    'bai' => $item->bai,
                    'title' => $item->title,
                    'content' => $item->content,
                    'section_key' => $item->section_key,
                ],
            ]);
        }

        if ($sectionType === 'speed_master_n5') {
            $bai = urldecode($itemKey);
            $result = $courseService->getSpeedMasterDetail($bai);

            return response()->json([
                'level' => $validatedLevel,
                'bai' => $bai,
                'title' => $result['title'],
                'groupedData' => $result['groupedData'],
            ]);
        }

        $fallback = N5CourseData::query()
            ->published()
            ->where('section_type', $sectionType)
            ->where('id', (int) $itemKey)
            ->first();

        if (! $fallback) {
            return response()->json(['message' => 'Không tìm thấy nội dung.'], 404);
        }

        return response()->json([
            'level' => $validatedLevel,
            'item' => [
                'id' => $fallback->id,
                'bai' => $fallback->bai,
                'title' => $fallback->title,
                'content' => $fallback->content,
                'section_key' => $fallback->section_key,
            ],
        ]);
    }

    public function search(Request $request, GlobalSearchService $searchService): JsonResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'max:80'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        if (mb_strlen(trim($data['q'])) < 2) {
            return response()->json($searchService->search('', $request->user(), 1));
        }

        return response()->json(
            $searchService->search($data['q'], $request->user(), (int) ($data['limit'] ?? 10))
        );
    }
}
