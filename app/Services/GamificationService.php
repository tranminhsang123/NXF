<?php

namespace App\Services;

use App\Models\Badge;
use App\Models\FlashcardCardState;
use App\Models\User;
use App\Models\UserProgress;
use Carbon\Carbon;

class GamificationService
{
    public function onFlashcardReviewed(User $user, int $quality): array
    {
        $user->refresh();
        $xp = $quality >= 3 ? 4 : 1;
        $user->increment('xp_total', $xp);
        $user->refresh();
        $this->touchStudyStreak($user);
        $newBadges = $this->evaluateBadges($user);

        return [
            'xp_gained' => $xp,
            'xp_total' => (int) $user->xp_total,
            'level' => $user->gamificationLevel(),
            'new_badges' => $newBadges,
        ];
    }

    public function onMinnaSectionCompleted(User $user): void
    {
        $user->refresh();
        $user->increment('xp_total', 8);
        $user->refresh();
        $this->touchStudyStreak($user);
        $this->evaluateBadges($user);
    }

    public function onMinnaLessonCompleted(User $user): void
    {
        $user->refresh();
        $user->increment('xp_total', 25);
        $user->refresh();
        $this->touchStudyStreak($user);
        $this->evaluateBadges($user);
    }

    /**
     * @return list<array{slug: string, name: string, icon: ?string}>
     */
    public function evaluateBadges(User $user): array
    {
        $user->loadMissing('badges');
        $owned = $user->badges->pluck('slug')->all();
        $awarded = [];

        $rules = [
            'first_review' => fn (User $u) => FlashcardCardState::where('user_id', $u->id)->exists(),
            'first_minna_lesson' => fn (User $u) => UserProgress::where('user_id', $u->id)
                ->where('lesson_type', UserProgress::TYPE_MINNA)
                ->where('status', UserProgress::STATUS_COMPLETED)
                ->exists(),
            'streak_3' => fn (User $u) => $u->current_streak >= 3,
            'streak_7' => fn (User $u) => $u->current_streak >= 7,
            'xp_200' => fn (User $u) => $u->xp_total >= 200,
            'xp_500' => fn (User $u) => $u->xp_total >= 500,
            'vocab_50' => fn (User $u) => FlashcardCardState::where('user_id', $u->id)->where('repetitions', '>', 0)->count() >= 50,
        ];

        foreach ($rules as $slug => $check) {
            if (in_array($slug, $owned, true)) {
                continue;
            }
            if (! $check($user)) {
                continue;
            }
            $badge = Badge::query()->where('slug', $slug)->first();
            if (! $badge) {
                continue;
            }
            if ($user->badges()->where('badges.id', $badge->id)->doesntExist()) {
                $user->badges()->attach($badge->id, ['earned_at' => now()]);
            }
            $awarded[] = [
                'slug' => $badge->slug,
                'name' => $badge->name,
                'icon' => $badge->icon,
            ];
            $owned[] = $slug;
        }

        return $awarded;
    }

    private function touchStudyStreak(User $user): void
    {
        $user->refresh();

        $today = Carbon::today();
        $lastRaw = $user->last_study_date;
        $last = $lastRaw ? Carbon::parse($lastRaw)->startOfDay() : null;

        if ($last && $last->equalTo($today)) {
            return;
        }

        if ($last === null) {
            $streak = 1;
        } elseif ($last->copy()->addDay()->equalTo($today)) {
            $streak = (int) $user->current_streak + 1;
        } else {
            $streak = 1;
        }

        $longest = max((int) $user->longest_streak, $streak);

        $user->forceFill([
            'current_streak' => $streak,
            'longest_streak' => $longest,
            'last_study_date' => $today->toDateString(),
        ])->saveQuietly();
    }
}
