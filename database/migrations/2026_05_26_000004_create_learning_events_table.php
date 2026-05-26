<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('event_type', 64);
            $table->string('subject_type', 64)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreignId('minna_lesson_id')->nullable()->constrained('minna_lessons')->nullOnDelete();
            $table->foreignId('minna_section_id')->nullable()->constrained('minna_sections')->nullOnDelete();
            $table->string('session_id', 120)->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
            $table->timestamps();

            $table->index(['user_id', 'event_type', 'occurred_at']);
            $table->index(['event_type', 'occurred_at']);
            $table->index(['minna_lesson_id', 'event_type']);
            $table->index(['minna_section_id', 'event_type']);
            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_events');
    }
};
