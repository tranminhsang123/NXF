<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pronunciation_audios', function (Blueprint $table) {
            $table->id();
            $table->string('text_hash', 64)->unique();
            $table->text('text');
            $table->string('language', 16)->default('ja-JP');
            $table->string('source', 32)->default('manual');
            $table->string('audio_url')->nullable();
            $table->json('metadata')->nullable();
            $table->unsignedInteger('usage_count')->default(0);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();

            $table->index(['language', 'source'], 'pronunciation_audio_language_source_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pronunciation_audios');
    }
};
