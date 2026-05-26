<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Http\Request;

class LeaderboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $leaders = User::query()
            ->where('role', 'user')
            ->orderByDesc('xp_total')
            ->orderByDesc('current_streak')
            ->orderBy('id')
            ->take(20)
            ->get(['id', 'name', 'xp_total', 'current_streak', 'longest_streak']);

        $completionCounts = UserProgress::query()
            ->whereIn('user_id', $leaders->pluck('id'))
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->selectRaw('user_id, count(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $rank = User::query()
            ->where('role', 'user')
            ->where('xp_total', '>', (int) ($user->xp_total ?? 0))
            ->count() + 1;

        return view('gamification.leaderboard', [
            'user' => $user,
            'leaders' => $leaders,
            'completionCounts' => $completionCounts,
            'rank' => $rank,
        ]);
    }
}
