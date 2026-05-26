<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('onboarding_level', 32)->nullable();
            $table->string('jlpt_goal', 16)->nullable();
            $table->unsignedSmallInteger('daily_study_minutes')->default(20);
            $table->boolean('email_reminders_enabled')->default(true);
            $table->timestamp('onboarding_completed_at')->nullable();
            $table->timestamp('last_study_reminder_sent_at')->nullable();
            $table->index(['email_reminders_enabled', 'last_study_date'], 'users_study_reminders_lookup_idx');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('users_study_reminders_lookup_idx');
            $table->dropColumn([
                'onboarding_level',
                'jlpt_goal',
                'daily_study_minutes',
                'email_reminders_enabled',
                'onboarding_completed_at',
                'last_study_reminder_sent_at',
            ]);
        });
    }
};
