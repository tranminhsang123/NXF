<?php

namespace App\Http\Controllers;

use App\Models\LearningEvent;
use App\Services\LearningEventService;
use App\Services\PracticalTopicService;
use Illuminate\Http\Request;

class PracticalTopicController extends Controller
{
    public function __construct(
        private PracticalTopicService $topicService,
        private LearningEventService $learningEventService,
    ) {}

    public function index(Request $request)
    {
        return view('topics.index', [
            'topics' => $this->topicService->all(),
            'recommendedTopics' => $this->topicService->recommendedFor($request->user()),
        ]);
    }

    public function show(Request $request, string $slug)
    {
        $topic = $this->topicService->find($slug);

        abort_if(! $topic, 404);

        $this->learningEventService->record($request->user(), LearningEvent::SECTION_VIEWED, [
            'subject_type' => 'practical_topic',
            'metadata' => [
                'topic_slug' => $topic['slug'],
                'topic_title' => $topic['title'],
            ],
        ], $request);

        return view('topics.show', [
            'topic' => $topic,
            'quizResult' => session('topic_quiz_result'),
        ]);
    }

    public function submitQuiz(Request $request, string $slug)
    {
        $result = $this->topicService->grade($slug, $request->input('answers', []));

        abort_if(! $result, 404);

        $this->learningEventService->record($request->user(), LearningEvent::QUIZ_SUBMITTED, [
            'subject_type' => 'practical_topic_quiz',
            'metadata' => [
                'topic_slug' => $slug,
                'score' => $result['score'],
                'total' => $result['total'],
                'percent' => $result['percent'],
                'passed' => $result['passed'],
            ],
        ], $request);

        return redirect()
            ->route('topics.show', ['slug' => $slug])
            ->with('topic_quiz_result', $result);
    }
}
