<?php

namespace App\Http\Controllers;

use App\Services\GlobalSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request, GlobalSearchService $searchService): View
    {
        $query = trim((string) $request->query('q', ''));
        $type = (string) $request->query('type', 'all');
        $results = null;

        if (mb_strlen($query) >= 2) {
            $results = $searchService->search($query, $request->user(), 12);
        }

        return view('search.index', [
            'query' => $query,
            'type' => $type,
            'results' => $results,
        ]);
    }

    public function api(Request $request, GlobalSearchService $searchService): JsonResponse
    {
        $data = $request->validate([
            'q' => ['required', 'string', 'max:80'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        return response()->json(
            $searchService->search($data['q'], $request->user(), (int) ($data['limit'] ?? 8))
        );
    }
}
