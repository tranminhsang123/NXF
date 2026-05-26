<?php

namespace App\Http\Controllers;

use App\Services\LearningReasonContentService;
use App\Services\OnboardingQuickWinService;
use App\Support\OnboardingOptions;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OnboardingController extends Controller
{
    public function edit(Request $request)
    {
        return view('auth.onboarding', [
            'user' => $request->user(),
            'levelOptions' => OnboardingOptions::levels(),
            'goalOptions' => OnboardingOptions::goals(),
            'dailyMinuteOptions' => OnboardingOptions::dailyMinuteOptions(),
            'learningReasonOptions' => OnboardingOptions::learningReasons(),
            'placementQuestions' => OnboardingOptions::placementQuestions(),
        ]);
    }

    public function result(
        Request $request,
        OnboardingQuickWinService $quickWinService,
        LearningReasonContentService $learningReasonContentService
    ) {
        $user = $request->user();
        [$quickWinRoute, $quickWinParameters] = $quickWinService->routeFor($user);

        return view('auth.onboarding-result', [
            'user' => $user,
            'summary' => OnboardingOptions::summaryFor($user),
            'placementBreakdown' => OnboardingOptions::placementBreakdown($user->placement_answers ?? []),
            'quickWinUrl' => route($quickWinRoute, $quickWinParameters),
            'reasonFocus' => $learningReasonContentService->profileFor($user),
            'totalPlacementQuestions' => count(OnboardingOptions::placementQuestions()),
        ]);
    }

    public function update(Request $request, OnboardingQuickWinService $quickWinService)
    {
        $data = $request->validate([
            'onboarding_level' => ['required', Rule::in(OnboardingOptions::levelKeys())],
            'jlpt_goal' => ['required', Rule::in(OnboardingOptions::goalKeys())],
            'daily_study_minutes' => ['required', 'integer', 'min:'.OnboardingOptions::MIN_DAILY_MINUTES, 'max:'.OnboardingOptions::MAX_DAILY_MINUTES],
            'learning_reasons' => ['nullable', 'array'],
            'learning_reasons.*' => ['string', Rule::in(OnboardingOptions::learningReasonKeys())],
            'placement_answers' => ['nullable', 'array'],
            'placement_answers.*' => ['nullable', 'string', 'max:100'],
            'email_reminders_enabled' => ['nullable', 'boolean'],
        ]);

        $placement = OnboardingOptions::evaluatePlacement($data['placement_answers'] ?? []);
        $level = $placement['answered'] ? $placement['level'] : $data['onboarding_level'];

        $request->user()->forceFill(OnboardingOptions::preferencesForCreate([
            'onboarding_level' => $level,
            'jlpt_goal' => $data['jlpt_goal'],
            'daily_study_minutes' => $data['daily_study_minutes'],
            'learning_reasons' => $data['learning_reasons'] ?? [],
            'placement_test_score' => $placement['score'],
            'placement_test_level' => $placement['level'],
            'placement_answers' => $placement['answers'],
            'email_reminders_enabled' => $request->has('email_reminders_enabled'),
        ]))->save();

        $quickWinService->markStarted($request->user());
        return redirect()
            ->route('onboarding.result')
            ->with('status', 'Đã cập nhật lộ trình cá nhân. Bắt đầu bằng một bài ngắn để lấy đà học ngay.');
    }
}
