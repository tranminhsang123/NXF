<?php

namespace App\Http\Controllers;

use App\Models\LearningEvent;
use App\Services\LearningEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LearningEventController extends Controller
{
    public function store(Request $request, LearningEventService $learningEventService): JsonResponse
    {
        $data = $request->validate([
            'event_type' => ['required', 'string', Rule::in(LearningEvent::allowedTypes())],
            'subject_type' => ['nullable', 'string', 'max:64'],
            'subject_id' => ['nullable', 'integer', 'min:1'],
            'minna_lesson_id' => ['nullable', 'integer', 'min:1'],
            'minna_section_id' => ['nullable', 'integer', 'min:1'],
            'metadata' => ['nullable', 'array'],
        ]);

        $event = $learningEventService->record(
            $request->user(),
            $data['event_type'],
            [
                'subject_type' => $data['subject_type'] ?? null,
                'subject_id' => $data['subject_id'] ?? null,
                'minna_lesson_id' => $data['minna_lesson_id'] ?? null,
                'minna_section_id' => $data['minna_section_id'] ?? null,
                'metadata' => $data['metadata'] ?? [],
            ],
            $request
        );

        return response()->json([
            'ok' => $event !== null,
            'event_id' => $event?->id,
        ], $event ? 201 : 422);
    }
}
