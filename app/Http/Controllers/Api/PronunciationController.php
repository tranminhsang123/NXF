<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PronunciationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PronunciationController extends Controller
{
    public function resolve(Request $request, PronunciationService $pronunciationService): JsonResponse
    {
        $data = $request->validate([
            'text' => ['required', 'string', 'max:500'],
            'language' => ['nullable', 'string', 'max:16'],
        ]);

        return response()->json([
            'audio' => $pronunciationService->resolve(
                $data['text'],
                $data['language'] ?? null
            ),
        ]);
    }
}
