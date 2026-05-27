<?php

namespace App\Http\Controllers;

use App\Models\LearningEvent;
use App\Services\AiTutorService;
use App\Services\LearningEventService;
use App\Services\MinnaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use InvalidArgumentException;

class AiTutorController extends Controller
{
    public function __invoke(
        Request $request,
        int $number,
        MinnaService $minnaService,
        AiTutorService $aiTutorService,
        LearningEventService $learningEventService
    ): JsonResponse {
        $data = $request->validate([
            'action' => ['required', 'string', Rule::in(AiTutorService::actions())],
            'prompt' => ['nullable', 'string', 'max:1600'],
            'selected_text' => ['nullable', 'string', 'max:800'],
            'section_key' => ['nullable', 'string', 'max:64'],
        ]);

        try {
            $lesson = $minnaService->getLessonByNumber($number);
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }

        $answer = $aiTutorService->answer($request->user(), $lesson, $data);

        $learningEventService->record($request->user(), LearningEvent::AI_TUTOR_USED, [
            'subject_type' => 'ai_tutor',
            'subject_id' => $lesson->id,
            'minna_lesson_id' => $lesson->id,
            'metadata' => [
                'action' => $data['action'],
                'provider' => $answer['provider'] ?? 'local',
                'model' => $answer['model'] ?? null,
                'section_key' => $data['section_key'] ?? null,
            ],
        ], $request);

        return response()->json([
            'ok' => true,
            'answer' => $answer,
        ]);
    }
}
