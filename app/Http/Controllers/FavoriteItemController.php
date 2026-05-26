<?php

namespace App\Http\Controllers;

use App\Models\FavoriteItem;
use App\Models\LearningEvent;
use App\Services\LearningEventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteItemController extends Controller
{
    public function store(Request $request, LearningEventService $learningEventService): JsonResponse
    {
        $data = $request->validate([
            'front' => ['required', 'string', 'max:500'],
            'back' => ['required', 'string', 'max:1000'],
            'item_type' => ['nullable', 'string', 'max:32'],
            'source_type' => ['nullable', 'string', 'max:32'],
            'source_id' => ['nullable', 'integer', 'min:1'],
            'lesson_number' => ['nullable', 'integer', 'min:1'],
            'metadata' => ['nullable', 'array'],
        ]);

        $user = $request->user();
        $front = trim($data['front']);
        $back = trim($data['back']);
        $itemKey = FavoriteItem::keyFor($user->id, $front, $back);

        $favorite = FavoriteItem::query()->updateOrCreate(
            [
                'user_id' => $user->id,
                'item_key' => $itemKey,
            ],
            [
                'item_type' => $data['item_type'] ?? 'vocabulary',
                'front' => $front,
                'back' => $back,
                'source_type' => $data['source_type'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'lesson_number' => $data['lesson_number'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ]
        );

        $learningEventService->record($user, LearningEvent::FAVORITE_SAVED, [
            'subject_type' => 'favorite_item',
            'subject_id' => $favorite->id,
            'metadata' => [
                'front' => $favorite->front,
                'item_type' => $favorite->item_type,
                'source_type' => $favorite->source_type,
                'source_id' => $favorite->source_id,
                'lesson_number' => $favorite->lesson_number,
                'created' => $favorite->wasRecentlyCreated,
            ],
        ], $request);

        return response()->json([
            'ok' => true,
            'favorite' => [
                'id' => $favorite->id,
                'front' => $favorite->front,
                'back' => $favorite->back,
            ],
        ], $favorite->wasRecentlyCreated ? 201 : 200);
    }

    public function destroy(Request $request, FavoriteItem $favoriteItem, LearningEventService $learningEventService): JsonResponse
    {
        abort_unless((int) $favoriteItem->user_id === (int) $request->user()->id, 403);

        $learningEventService->record($request->user(), LearningEvent::FAVORITE_REMOVED, [
            'subject_type' => 'favorite_item',
            'subject_id' => $favoriteItem->id,
            'metadata' => [
                'front' => $favoriteItem->front,
                'item_type' => $favoriteItem->item_type,
                'lesson_number' => $favoriteItem->lesson_number,
            ],
        ], $request);

        $favoriteItem->delete();

        return response()->json(['ok' => true]);
    }
}
