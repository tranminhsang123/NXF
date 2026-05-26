<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DictionaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DictionaryController extends Controller
{
    public function lookup(Request $request, DictionaryService $dictionaryService): JsonResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'max:80'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        return response()->json(
            $dictionaryService->lookup($data['q'], (int) ($data['limit'] ?? 8))
        );
    }
}
