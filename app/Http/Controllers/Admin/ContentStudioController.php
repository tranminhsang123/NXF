<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MinnaLesson;
use App\Services\AdminContentStudioService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ContentStudioController extends Controller
{
    public function __construct(private AdminContentStudioService $studioService) {}

    public function index(Request $request)
    {
        $filters = [
            'q' => $request->query('q'),
            'status' => $request->query('status'),
            'quality' => $request->query('quality'),
        ];
        $lessons = $this->studioService->lessons($filters, 20);
        $diagnosticsByLesson = $lessons->getCollection()
            ->mapWithKeys(fn (MinnaLesson $lesson) => [$lesson->id => $this->studioService->diagnostics($lesson)]);

        return view('admin.content-studio.index', [
            'lessons' => $lessons,
            'diagnosticsByLesson' => $diagnosticsByLesson,
            'overview' => $this->studioService->overview(),
            'statuses' => \App\Support\PublishStatus::labels(),
        ]);
    }

    public function createFromTemplate(Request $request)
    {
        $data = $request->validate([
            'number' => ['nullable', 'integer', 'min:1', Rule::unique('minna_lessons', 'number')],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $lesson = $this->studioService->createFromTemplate($data);

        return redirect()
            ->route('admin.content-studio.index', ['q' => $lesson->number])
            ->with('success', 'Đã tạo bài '.$lesson->number.' theo template, gồm đủ 5 phần học cơ bản.');
    }

    public function import(Request $request)
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'max:5120'],
            'lesson_number' => ['nullable', 'integer', 'min:1'],
        ]);

        $result = $this->studioService->importFile(
            $data['file'],
            isset($data['lesson_number']) ? (int) $data['lesson_number'] : null
        );

        return redirect()
            ->route('admin.content-studio.index')
            ->with('success', 'Đã import '.$result['item_count'].' mục vào '.$result['lesson_count'].' bài học.')
            ->with('import_result', $result);
    }

    public function generateQuiz(MinnaLesson $lesson)
    {
        $result = $this->studioService->generateQuiz($lesson);

        return redirect()
            ->route('admin.content-studio.index', ['q' => $lesson->number])
            ->with('success', 'Đã tạo '.$result['mini_quiz_count'].' câu mini quiz và '.$result['advanced_quiz_count'].' câu quiz nâng cao.');
    }

    public function generateFlashcards(MinnaLesson $lesson)
    {
        $result = $this->studioService->generateFlashcards($lesson);

        return redirect()
            ->route('admin.content-studio.index', ['q' => $lesson->number])
            ->with('success', 'Đã tạo '.$result['card_count'].' flashcard từ từ vựng của bài.');
    }

    public function preview(MinnaLesson $lesson)
    {
        return view('admin.content-studio.preview-user', $this->studioService->userPreviewData($lesson));
    }

    public function compare(MinnaLesson $lesson)
    {
        return view('admin.content-studio.compare', [
            'lesson' => $lesson,
            'compare' => $this->studioService->versionCompare($lesson),
        ]);
    }
}
