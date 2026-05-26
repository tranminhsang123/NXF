<?php

namespace App\Http\Controllers;

use App\Services\PersonalizedRoadmapService;
use Illuminate\Http\Request;

class QuickWinController extends Controller
{
    public function congrats(Request $request, PersonalizedRoadmapService $roadmapService)
    {
        $user = $request->user()->fresh();
        $roadmap = $roadmapService->build($user);
        $nextSection = $roadmap['next_section'] ?? null;

        return view('user.quick-win-congrats', [
            'user' => $user,
            'lessonNumber' => $request->integer('lesson') ?: null,
            'roadmap' => $roadmap,
            'nextUrl' => $nextSection
                ? route('minna.section', [
                    'number' => $nextSection['lesson_number'] ?? 1,
                    'sectionKey' => $nextSection['section_key'] ?? '',
                ])
                : route('user.dashboard'),
        ]);
    }
}
