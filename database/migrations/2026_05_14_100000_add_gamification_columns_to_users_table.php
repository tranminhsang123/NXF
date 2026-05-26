<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('xp_total')->default(0)->after('remember_token');
            $table->unsignedSmallInteger('current_streak')->default(0)->after('xp_total');
            $table->unsignedSmallInteger('longest_streak')->default(0)->after('current_streak');
            $table->date('last_study_date')->nullable()->after('longest_streak');
            $table->unsignedTinyInteger('daily_goal_minna_lessons')->default(1)->after('last_study_date');
            $table->unsignedSmallInteger('daily_goal_flashcards')->default(12)->after('daily_goal_minna_lessons');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'xp_total',
                'current_streak',
                'longest_streak',
                'last_study_date',
                'daily_goal_minna_lessons',
                'daily_goal_flashcards',
            ]);
        });
    }
};
