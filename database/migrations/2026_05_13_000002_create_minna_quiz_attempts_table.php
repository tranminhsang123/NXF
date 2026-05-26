<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('minna_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('minna_lesson_id')->constrained('minna_lessons')->cascadeOnDelete();
            $table->unsignedInteger('score');
            $table->unsignedInteger('total');
            $table->unsignedInteger('percent');
            $table->boolean('passed')->default(false);
            $table->json('answers_snapshot')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'completed_at'], 'minna_quiz_attempts_user_completed_idx');
            $table->index(['user_id', 'minna_lesson_id'], 'minna_quiz_attempts_user_lesson_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('minna_quiz_attempts');
    }
};
