<?php

namespace App\Http\Controllers;

use App\Models\LearningEvent;
use App\Models\MinnaLesson;
use App\Models\MinnaQuizAttempt;
use App\Models\MinnaSection;
use App\Models\UserMinnaSectionProgress;
use App\Models\UserProgress;
use App\Services\AdvancedQuizService;
use App\Services\LearningEventService;
use App\Services\MinnaService;
use App\Services\OnboardingQuickWinService;
use App\Services\UserProgressService;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

class MinnaController extends Controller
{
    public function __construct(
        private MinnaService $minnaService,
        private UserProgressService $userProgressService,
        private AdvancedQuizService $advancedQuizService,
        private OnboardingQuickWinService $quickWinService,
        private LearningEventService $learningEventService
    ) {}

    /**
     * Hiển thị danh sách tất cả các bài học
     */
    public function index(Request $request)
    {
        $lessons = $this->minnaService->getAllLessons();
        $progressByLessonId = collect();
        $sectionProgressByLessonId = collect();
        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'status' => (string) $request->query('status', 'all'),
        ];

        if (Auth::check()) {
            $progressByLessonId = UserProgress::query()
                ->where('user_id', Auth::id())
                ->where('lesson_type', UserProgress::TYPE_MINNA)
                ->get()
                ->keyBy('lesson_id');

            $lessonIds = $lessons->pluck('id')->all();
            $sectionCounts = MinnaSection::query()
                ->published()
                ->whereIn('lesson_id', $lessonIds)
                ->selectRaw('lesson_id, count(*) as total_sections')
                ->groupBy('lesson_id')
                ->pluck('total_sections', 'lesson_id');

            $completedCounts = UserMinnaSectionProgress::query()
                ->where('user_id', Auth::id())
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
        }

        if ($filters['q'] !== '') {
            $needle = mb_strtolower($filters['q']);
            $lessons = $lessons->filter(function ($lesson) use ($needle) {
                return str_contains((string) $lesson->number, $needle)
                    || str_contains(mb_strtolower((string) $lesson->title), $needle)
                    || str_contains(mb_strtolower((string) $lesson->description), $needle);
            })->values();
        }

        if (in_array($filters['status'], ['not_started', 'in_progress', 'completed'], true)) {
            $lessons = $lessons->filter(function ($lesson) use ($filters, $progressByLessonId) {
                $progress = $progressByLessonId->get($lesson->id);

                if ($filters['status'] === 'not_started') {
                    return ! $progress;
                }

                return $progress && $progress->status === $filters['status'];
            })->values();
        }
        
        return view('minna.index', compact('lessons', 'progressByLessonId', 'sectionProgressByLessonId', 'filters'));
    }

    public function roadmap()
    {
        $lessons = $this->minnaService->getAllLessons();
        $progressByLessonId = collect();
        $sectionProgressByLessonId = collect();

        if (Auth::check()) {
            $progressByLessonId = UserProgress::query()
                ->where('user_id', Auth::id())
                ->where('lesson_type', UserProgress::TYPE_MINNA)
                ->get()
                ->keyBy('lesson_id');

            $lessonIds = $lessons->pluck('id')->all();
            $sectionCounts = MinnaSection::query()
                ->published()
                ->whereIn('lesson_id', $lessonIds)
                ->selectRaw('lesson_id, count(*) as total_sections')
                ->groupBy('lesson_id')
                ->pluck('total_sections', 'lesson_id');
            $completedCounts = UserMinnaSectionProgress::query()
                ->where('user_id', Auth::id())
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
        }

        return view('minna.roadmap', compact('lessons', 'progressByLessonId', 'sectionProgressByLessonId'));
    }

    /**
     * Hiển thị chi tiết một bài học
     */
    public function show(Request $request, $number)
    {
        try {
            $lesson = $this->minnaService->getLessonByNumber($number);
            $sectionsByKey = $this->minnaService->groupSectionsByKey($lesson->sections);
            $previousLessonNumber = $this->minnaService->getPreviousLessonNumber($lesson->number);
            $nextLessonNumber = $this->minnaService->getNextLessonNumber($lesson->number);

            $progress = null;
            $sectionProgressById = collect();
            $sectionSummary = ['total' => 0, 'completed' => 0, 'percent' => 0];
            $quizAttempts = collect();
            if (Auth::check()) {
                $progress = $this->userProgressService->touchMinnaLesson(Auth::user(), $lesson);
                $sectionProgressById = UserMinnaSectionProgress::query()
                    ->where('user_id', Auth::id())
                    ->where('minna_lesson_id', $lesson->id)
                    ->get()
                    ->keyBy('minna_section_id');
                $sectionSummary = $this->userProgressService->getMinnaLessonSectionSummary(Auth::user(), $lesson);
                $quizAttempts = MinnaQuizAttempt::query()
                    ->where('user_id', Auth::id())
                    ->where('minna_lesson_id', $lesson->id)
                    ->latest('completed_at')
                    ->take(5)
                    ->get();
            }

            $this->learningEventService->record($request->user(), LearningEvent::LESSON_VIEWED, [
                'subject_type' => 'minna_lesson',
                'subject_id' => $lesson->id,
                'minna_lesson_id' => $lesson->id,
                'metadata' => [
                    'lesson_number' => $lesson->number,
                    'lesson_title' => $lesson->title,
                ],
            ], $request);

            $quizQuestions = $this->buildQuizQuestions($lesson->sections);

            return view('minna.show', compact(
                'lesson',
                'sectionsByKey',
                'previousLessonNumber',
                'nextLessonNumber',
                'progress',
                'sectionProgressById',
                'sectionSummary',
                'quizQuestions',
                'quizAttempts'
            ));
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Hiển thị một section cụ thể của bài học
     */
    public function showSection(Request $request, $number, $sectionKey)
    {
        try {
            $section = $this->minnaService->getSectionByLessonAndKey($number, $sectionKey);
            $lesson = $section->lesson;

            if (Auth::check()) {
                $this->userProgressService->touchMinnaLesson(Auth::user(), $lesson);
            }

            $this->learningEventService->record($request->user(), LearningEvent::SECTION_VIEWED, [
                'subject_type' => 'minna_section',
                'subject_id' => $section->id,
                'minna_lesson_id' => $lesson->id,
                'minna_section_id' => $section->id,
                'metadata' => [
                    'lesson_number' => $lesson->number,
                    'section_key' => $section->key,
                    'section_title' => $section->title,
                ],
            ], $request);

            return view('minna.section', compact('lesson', 'section'));
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Đánh dấu bài học là đã hoàn thành.
     */
    public function complete($number)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            /** @var MinnaLesson $lesson */
            $lesson = $this->minnaService->getLessonByNumber((int) $number);

            $user = Auth::user();
            $this->userProgressService->markMinnaLessonCompleted($user, $lesson);
            $this->learningEventService->record($user, LearningEvent::LESSON_COMPLETED, [
                'subject_type' => 'minna_lesson',
                'subject_id' => $lesson->id,
                'minna_lesson_id' => $lesson->id,
                'metadata' => [
                    'lesson_number' => $lesson->number,
                ],
            ], request());

            if ($this->quickWinService->markCompleted($user)) {
                return redirect()
                    ->route('quick-win.congrats', ['lesson' => $lesson->number])
                    ->with('status', 'Bạn vừa hoàn thành quick win đầu tiên.');
            }

            return redirect()
                ->route('minna.show', ['number' => $lesson->number])
                ->with('status', 'Đã đánh dấu bài học là hoàn thành.');
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function completeSection(int $number, int $section)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $lesson = $this->minnaService->getLessonByNumber($number);
            $minnaSection = $lesson->sections()->where('id', $section)->firstOrFail();

            $user = Auth::user();
            $this->userProgressService->markMinnaSectionCompleted($user, $minnaSection);
            $this->learningEventService->record($user, LearningEvent::SECTION_COMPLETED, [
                'subject_type' => 'minna_section',
                'subject_id' => $minnaSection->id,
                'minna_lesson_id' => $lesson->id,
                'minna_section_id' => $minnaSection->id,
                'metadata' => [
                    'lesson_number' => $lesson->number,
                    'section_key' => $minnaSection->key,
                    'section_title' => $minnaSection->title,
                ],
            ], request());

            if ($this->quickWinService->markCompleted($user)) {
                return redirect()
                    ->route('quick-win.congrats', ['lesson' => $lesson->number, 'section' => $minnaSection->id])
                    ->with('status', 'Bạn vừa hoàn thành quick win đầu tiên.');
            }

            return redirect()
                ->route('minna.show', ['number' => $lesson->number])
                ->with('status', 'Đã đánh dấu xong phần '.$minnaSection->title.'.');
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function submitQuiz(Request $request, int $number)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $lesson = $this->minnaService->getLessonByNumber($number);
            $questions = $this->buildQuizQuestions($lesson->sections);
            $answers = $request->input('answers', []);
            $correct = 0;

            foreach ($questions as $index => $question) {
                if (($answers[$index] ?? null) === $question['answer']) {
                    $correct++;
                }
            }

            $total = count($questions);
            $percent = $total > 0 ? (int) round(($correct / $total) * 100) : 0;
            $passed = $total > 0 && $percent >= 80;

            $attempt = MinnaQuizAttempt::query()->create([
                'user_id' => Auth::id(),
                'minna_lesson_id' => $lesson->id,
                'score' => $correct,
                'total' => $total,
                'percent' => $percent,
                'passed' => $passed,
                'answers_snapshot' => collect($questions)->map(function ($question, $index) use ($answers) {
                    return [
                        'prompt' => $question['prompt'],
                        'answer' => $question['answer'],
                        'selected' => $answers[$index] ?? null,
                        'correct' => ($answers[$index] ?? null) === $question['answer'],
                    ];
                })->values()->all(),
                'completed_at' => now(),
            ]);

            $this->learningEventService->record($request->user(), LearningEvent::QUIZ_SUBMITTED, [
                'subject_type' => 'minna_quiz_attempt',
                'subject_id' => $attempt->id,
                'minna_lesson_id' => $lesson->id,
                'metadata' => [
                    'lesson_number' => $lesson->number,
                    'score' => $correct,
                    'total' => $total,
                    'percent' => $percent,
                    'passed' => $passed,
                ],
            ], $request);

            if ($passed) {
                $this->userProgressService->markMinnaLessonCompleted(Auth::user(), $lesson);

                return redirect()
                    ->route('minna.show', ['number' => $lesson->number])
                    ->with('status', 'Quiz đạt '.$percent.'%. Bài học đã được đánh dấu hoàn thành.');
            }

            return redirect()
                ->route('minna.show', ['number' => $lesson->number])
                ->with('warning', 'Quiz đạt '.$percent.'%. Cần đạt tối thiểu 80% để hoàn thành bài.');
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function advancedQuiz(int $number)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $lesson = $this->minnaService->getLessonByNumber($number);
            $questions = $this->advancedQuizService->build($lesson);

            return view('minna.advanced-quiz', [
                'lesson' => $lesson,
                'questions' => $questions,
            ]);
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    public function submitAdvancedQuiz(Request $request, int $number)
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $lesson = $this->minnaService->getLessonByNumber($number);
            $questions = $this->advancedQuizService->build($lesson);
            $result = $this->advancedQuizService->grade($questions, $request->input('answers', []));

            $attempt = MinnaQuizAttempt::query()->create([
                'user_id' => Auth::id(),
                'minna_lesson_id' => $lesson->id,
                'score' => $result['score'],
                'total' => $result['total'],
                'percent' => $result['percent'],
                'passed' => $result['passed'],
                'answers_snapshot' => [
                    'mode' => 'advanced',
                    'answers' => $result['answers_snapshot'],
                ],
                'completed_at' => now(),
            ]);

            $this->learningEventService->record($request->user(), LearningEvent::ADVANCED_QUIZ_SUBMITTED, [
                'subject_type' => 'minna_quiz_attempt',
                'subject_id' => $attempt->id,
                'minna_lesson_id' => $lesson->id,
                'metadata' => [
                    'lesson_number' => $lesson->number,
                    'score' => $result['score'],
                    'total' => $result['total'],
                    'percent' => $result['percent'],
                    'passed' => $result['passed'],
                ],
            ], $request);

            if ($result['passed']) {
                $this->userProgressService->markMinnaLessonCompleted(Auth::user(), $lesson);

                return redirect()
                    ->route('minna.show', ['number' => $lesson->number])
                    ->with('status', 'Quiz nâng cao đạt '.$result['percent'].'%. Bài học đã được đánh dấu hoàn thành.');
            }

            return redirect()
                ->route('minna.quiz.advanced', ['number' => $lesson->number])
                ->with('warning', 'Quiz nâng cao đạt '.$result['percent'].'%. Cần đạt tối thiểu 75%.');
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    private function buildQuizQuestions(Collection $sections): array
    {
        $items = [];
        foreach ($sections->where('key', 'tu-vung') as $section) {
            foreach (['vocab', 'mau_cau', 'countries', 'proper_nouns', 'cau', 'places', 'rail'] as $contentKey) {
                foreach (($section->content[$contentKey] ?? []) as $item) {
                    $front = $item['tu_vung'] ?? $item['jp'] ?? null;
                    $answer = $item['nghia'] ?? null;
                    if ($front && $answer) {
                        $items[] = ['prompt' => $front, 'answer' => $answer];
                    }
                }
            }
        }

        if (count($items) < 4) {
            return [];
        }

        $questions = [];
        $answers = array_values(array_unique(array_column($items, 'answer')));
        foreach (array_slice($items, 0, 5) as $idx => $item) {
            $options = [$item['answer']];
            $cursor = $idx + 1;
            while (count($options) < 4 && count($options) < count($answers)) {
                $candidate = $answers[$cursor % count($answers)];
                if (! in_array($candidate, $options, true)) {
                    $options[] = $candidate;
                }
                $cursor++;
            }
            sort($options);

            $questions[] = [
                'prompt' => $item['prompt'],
                'answer' => $item['answer'],
                'options' => $options,
            ];
        }

        return $questions;
    }
}
