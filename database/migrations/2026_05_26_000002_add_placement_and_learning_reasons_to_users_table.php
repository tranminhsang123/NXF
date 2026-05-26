<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('learning_reasons')->nullable()->after('daily_study_minutes');
            $table->unsignedTinyInteger('placement_test_score')->nullable()->after('learning_reasons');
            $table->string('placement_test_level', 32)->nullable()->after('placement_test_score');
            $table->json('placement_answers')->nullable()->after('placement_test_level');
            $table->timestamp('quick_win_started_at')->nullable()->after('onboarding_completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'learning_reasons',
                'placement_test_score',
                'placement_test_level',
                'placement_answers',
                'quick_win_started_at',
            ]);
        });
    }
};
