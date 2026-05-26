<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_minna_section_progresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('minna_lesson_id')->constrained('minna_lessons')->cascadeOnDelete();
            $table->foreignId('minna_section_id')->constrained('minna_sections')->cascadeOnDelete();
            $table->string('section_key', 80);
            $table->string('status', 30)->default('completed');
            $table->timestamp('last_accessed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'minna_section_id'], 'user_minna_section_unique');
            $table->index(['user_id', 'minna_lesson_id'], 'user_minna_lesson_sections_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_minna_section_progresses');
    }
};
