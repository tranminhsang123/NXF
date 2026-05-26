<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('chat:retry-pending-events --limit=200')
            ->everyMinute()
            ->withoutOverlapping();

        $schedule->command('chat:reconcile-state --days=14 --limit=500')
            ->dailyAt('03:15')
            ->withoutOverlapping();

        $schedule->command('chat:cleanup-idempotency --limit=5000')
            ->dailyAt('03:30')
            ->withoutOverlapping();

        $schedule->command('learning:send-streak-reminders --limit=1000')
            ->dailyAt('20:00')
            ->timezone('Asia/Tokyo')
            ->withoutOverlapping();

        $schedule->command('content:publish-scheduled --limit=200')
            ->everyFiveMinutes()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
