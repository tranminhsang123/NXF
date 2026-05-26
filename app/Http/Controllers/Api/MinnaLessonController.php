<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MinnaLesson;
use App\Services\UserProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MinnaLessonController extends Controller
{
    public function index(): JsonResponse
    {
        $lessons = MinnaLesson::query()
            ->orderBy('number')
            ->withCount('sections')
            ->get([
                'id',
                'number',
                'title',
                'description',
            ]);

        return response()->json([
            'lessons' => $lessons,
        ]);
    }

    public function show(Request $request, int $number, UserProgressService $userProgressService): JsonResponse
    {
        $lesson = MinnaLesson::query()
            ->where('number', $number)
            ->with(['sections' => function ($query) {
                $query
                    ->select('id', 'lesson_id', 'order_index', 'key', 'title', 'content', 'media_url')
                    ->orderBy('order_index');
            }])
            ->first();

        if (! $lesson) {
            return response()->json([
                'message' => 'Không tìm thấy bài học.',
            ], 404);
        }

        $user = $request->user();
        if ($user) {
            $userProgressService->touchMinnaLesson($user, $lesson);
        }

        return response()->json([
            'lesson' => $lesson,
        ]);
    }
}
