<?php

namespace App\Console\Commands;

use App\Mail\StreakReminderMail;
use App\Models\Notification;
use App\Models\User;
use App\Services\LearningReasonContentService;
use App\Services\PersonalizedRoadmapService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendStreakReminders extends Command
{
    protected $signature = 'learning:send-streak-reminders {--limit=500 : Max users per run} {--dry-run : Show count without sending}';

    protected $description = 'Send email and in-app reminders to users whose study streak is at risk.';

    public function handle(
        PersonalizedRoadmapService $roadmapService,
        LearningReasonContentService $learningReasonContentService
    ): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $today = Carbon::today();
        $yesterday = $today->copy()->subDay();

        $query = User::query()
            ->where('role', 'user')
            ->where('email_reminders_enabled', true)
            ->where('current_streak', '>', 0)
            ->whereDate('last_study_date', $yesterday->toDateString())
            ->where(function ($q) use ($today) {
                $q->whereNull('last_study_reminder_sent_at')
                    ->orWhereDate('last_study_reminder_sent_at', '<', $today->toDateString());
            })
            ->orderBy('id')
            ->limit($limit);

        if ($this->option('dry-run')) {
            $this->info('Users at risk: '.$query->count());

            return self::SUCCESS;
        }

        $sent = 0;
        $failed = 0;

        $query->each(function (User $user) use ($roadmapService, $learningReasonContentService, &$sent, &$failed) {
            $roadmap = $roadmapService->build($user);
            $reminderMessage = $learningReasonContentService->reminderMessageFor($user);

            $notification = Notification::createForUser(
                $user,
                'streak_at_risk',
                'Streak sắp đứt',
                'Hãy ôn SRS hoặc hoàn thành một phần Minna hôm nay để giữ streak.',
                [
                    'current_streak' => (int) $user->current_streak,
                    'next_section' => $roadmap['next_section'] ?? null,
                ]
            );

            $notification->forceFill([
                'message' => $reminderMessage,
                'data' => array_merge($notification->data ?? [], [
                    'learning_reason_message' => $reminderMessage,
                ]),
            ])->save();

            try {
                Mail::to($user->email)->send(new StreakReminderMail($user, $roadmap));
                $sent++;
            } catch (\Throwable $e) {
                $failed++;
                Log::warning('learning.streak_reminder_mail_failed', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }

            $user->forceFill([
                'last_study_reminder_sent_at' => now(),
            ])->saveQuietly();
        });

        $this->info("Streak reminders processed. sent={$sent}, failed={$failed}");

        return self::SUCCESS;
    }
}
