<?php

namespace App\Services;

use App\Models\FlashcardCardState;
use App\Models\User;
use Carbon\Carbon;

/**
 * Thuật toán SM-2 (SuperMemo 2) — quality 0–5.
 */
class SpacedRepetitionService
{
    private const MIN_EASE = 1.3;

    /**
     * @return array{ease_factor: float, repetitions: int, interval_days: float, next_review_at: Carbon, lapses: int}
     */
    public function applyReview(?FlashcardCardState $state, int $quality): array
    {
        $quality = max(0, min(5, $quality));

        $ef = $state?->ease_factor ?? 2.5;
        $oldReps = $state?->repetitions ?? 0;
        $oldInterval = (float) ($state?->interval_days ?? 0);
        $lapses = (int) ($state?->lapses ?? 0);

        $ef = $ef + (0.1 - (5 - $quality) * (0.08 + (5 - $quality) * 0.02));
        if ($ef < self::MIN_EASE) {
            $ef = self::MIN_EASE;
        }

        if ($quality < 3) {
            $lapses++;
            $ef = max(self::MIN_EASE, $ef - min(0.45, 0.06 * $lapses));
            $reps = 0;
            $interval = 1.0;
        } else {
            if ($oldReps === 0) {
                $interval = 1.0;
            } elseif ($oldReps === 1) {
                $interval = 6.0;
            } else {
                $boost = 1.0 + 0.04 * min(5, $quality) - 0.12;
                $interval = max(1.0, round($oldInterval * $ef * $boost, 2));
            }
            $reps = $oldReps + 1;
        }

        $next = Carbon::now()->addDays((int) ceil($interval));

        return [
            'ease_factor' => round($ef, 2),
            'repetitions' => $reps,
            'interval_days' => $interval,
            'next_review_at' => $next,
            'lapses' => $lapses,
        ];
    }

    public function recordReview(User $user, int $minnaSectionId, int $cardIndex, int $quality): FlashcardCardState
    {
        $quality = max(0, min(5, $quality));

        $state = FlashcardCardState::query()->firstOrNew([
            'user_id' => $user->id,
            'minna_section_id' => $minnaSectionId,
            'card_index' => $cardIndex,
        ]);

        $result = $this->applyReview($state->exists ? $state : null, $quality);

        $state->fill([
            'ease_factor' => $result['ease_factor'],
            'repetitions' => $result['repetitions'],
            'interval_days' => $result['interval_days'],
            'next_review_at' => $result['next_review_at'],
            'last_reviewed_at' => now(),
            'last_quality' => $quality,
            'lapses' => $result['lapses'],
        ]);
        $state->save();

        return $state;
    }
}
