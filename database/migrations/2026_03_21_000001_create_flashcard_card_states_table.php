<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Trạng thái ôn spaced repetition (SM-2) theo user + thẻ (section từ vựng Minna + chỉ số thẻ).
     */
    public function up(): void
    {
        Schema::create('flashcard_card_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('minna_section_id')->constrained('minna_sections')->cascadeOnDelete();
            $table->unsignedInteger('card_index');
            $table->decimal('ease_factor', 4, 2)->default(2.50);
            $table->unsignedInteger('repetitions')->default(0);
            $table->decimal('interval_days', 8, 2)->default(0);
            $table->timestamp('next_review_at')->nullable();
            $table->timestamp('last_reviewed_at')->nullable();
            $table->unsignedTinyInteger('last_quality')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'minna_section_id', 'card_index'], 'flashcard_card_states_user_section_index_uq');
            $table->index(['user_id', 'next_review_at'], 'flashcard_card_states_user_next_review_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flashcard_card_states');
    }
};
