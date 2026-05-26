<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFlashcardReviewRequest;
use App\Services\FlashcardService;
use App\Services\GamificationService;
use App\Services\SpacedRepetitionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FlashcardApiController extends Controller
{
    public function study(Request $request, FlashcardService $flashcardService): JsonResponse
    {
        $numbers = $request->input('lesson_numbers', []);
        if (! is_array($numbers)) {
            $numbers = [];
        }

        $numbers = array_values(array_unique(array_filter(array_map('intval', $numbers))));
        if (empty($numbers)) {
            $numbers = [1];
        }

        $shuffle = (bool) $request->boolean('shuffle', false);
        $srsMode = $request->query('mode') === 'srs';

        if ($srsMode) {
            $result = $flashcardService->getFlashcardsForSrs($request->user(), $numbers);
        } else {
            $result = $flashcardService->getFlashcardsByLessons($numbers, $shuffle);
            $result['stats'] = null;
        }

        return response()->json($result);
    }

    public function favorites(Request $request, FlashcardService $flashcardService): JsonResponse
    {
        return response()->json($flashcardService->getFavoriteFlashcards($request->user()));
    }

    public function review(
        StoreFlashcardReviewRequest $request,
        SpacedRepetitionService $spacedRepetitionService
    ): JsonResponse {
        $data = $request->validated();
        $state = $spacedRepetitionService->recordReview(
            $request->user(),
            (int) $data['minna_section_id'],
            (int) $data['card_index'],
            (int) $data['quality']
        );

        $gamification = app(GamificationService::class)->onFlashcardReviewed(
            $request->user(),
            (int) $data['quality']
        );

        return response()->json([
            'ok' => true,
            'next_review_at' => $state->next_review_at?->toIso8601String(),
            'interval_days' => $state->interval_days,
            'repetitions' => $state->repetitions,
            'lapses' => $state->lapses,
            'gamification' => $gamification,
        ]);
    }
}
