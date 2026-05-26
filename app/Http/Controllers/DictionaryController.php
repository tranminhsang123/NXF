<?php

namespace App\Http\Controllers;

use App\Models\LearningEvent;
use App\Services\DictionaryService;
use App\Services\LearningEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DictionaryController extends Controller
{
    public function lookup(
        Request $request,
        DictionaryService $dictionaryService,
        LearningEventService $learningEventService
    ): JsonResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'max:80'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $result = $dictionaryService->lookup($data['q'], (int) ($data['limit'] ?? 8));

        $learningEventService->record($request->user(), LearningEvent::DICTIONARY_LOOKUP, [
            'subject_type' => 'dictionary',
            'metadata' => [
                'query' => $data['q'],
                'limit' => (int) ($data['limit'] ?? 8),
                'entries_count' => count($result['entries'] ?? []),
                'kanji_count' => count($result['kanji'] ?? []),
            ],
        ], $request);

        return response()->json($result);
    }
}
