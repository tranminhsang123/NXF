<?php

namespace App\Services;

use App\Models\MinnaLesson;
use App\Models\User;
use App\Support\OnboardingOptions;

class OnboardingQuickWinService
{
    public function routeFor(User $user): array
    {
        $startLesson = OnboardingOptions::startLessonNumber($user->onboarding_level);
        $lesson = MinnaLesson::query()
            ->published()
            ->where('number', '>=', $startLesson)
            ->orderBy('number')
            ->with(['sections' => fn ($query) => $query->published()->orderBy('order_index')])
            ->first();

        if (! $lesson && $startLesson > 1) {
            $lesson = MinnaLesson::query()
                ->published()
                ->orderBy('number')
                ->with(['sections' => fn ($query) => $query->published()->orderBy('order_index')])
                ->first();
        }

        if (! $lesson) {
            return ['user.dashboard', []];
        }

        $section = $lesson->sections->first();
        if ($section) {
            return [
                'minna.section',
                ['number' => $lesson->number, 'sectionKey' => $section->key],
            ];
        }

        return ['minna.show', ['number' => $lesson->number]];
    }

    public function markStarted(User $user): void
    {
        if ($user->quick_win_started_at) {
            return;
        }

        $user->forceFill(['quick_win_started_at' => now()])->save();
    }

    public function markCompleted(User $user): bool
    {
        $user->refresh();

        if (! $user->quick_win_started_at || $user->quick_win_completed_at) {
            return false;
        }

        $user->forceFill(['quick_win_completed_at' => now()])->save();

        return true;
    }
}
