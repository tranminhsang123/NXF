<?php

namespace App\Http\Controllers;

use App\Models\UserProgress;
use Illuminate\Http\Request;

class AchievementShareController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();
        $user->loadMissing('badges');

        $completedLessons = UserProgress::query()
            ->where('user_id', $user->id)
            ->where('lesson_type', UserProgress::TYPE_MINNA)
            ->where('status', UserProgress::STATUS_COMPLETED)
            ->count();

        $shareText = 'Mình đang học tiếng Nhật: '
            .(int) ($user->xp_total ?? 0).' XP, streak '
            .(int) ($user->current_streak ?? 0).' ngày, đã hoàn thành '
            .$completedLessons.' bài Minna.';

        return view('gamification.share', [
            'user' => $user,
            'completedLessons' => $completedLessons,
            'shareText' => $shareText,
        ]);
    }
}
