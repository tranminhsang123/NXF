<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_publish_requests', function (Blueprint $table) {
            $table->id();
            $table->string('content_type', 64);
            $table->unsignedBigInteger('content_id');
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('requested_status', 24)->default('published');
            $table->string('status', 24)->default('pending')->index();
            $table->text('notes')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamp('scheduled_publish_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['content_type', 'content_id'], 'content_publish_subject_idx');
            $table->index(['status', 'scheduled_publish_at'], 'content_publish_schedule_idx');
        });

        Schema::create('chat_message_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_message_id')->constrained('chat_messages')->cascadeOnDelete();
            $table->foreignId('group_id')->constrained('chat_groups')->cascadeOnDelete();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 24)->default('pending')->index();
            $table->string('reason', 500);
            $table->text('resolution_note')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->unique(['chat_message_id', 'reporter_id'], 'chat_message_reports_unique_reporter');
            $table->index(['group_id', 'status'], 'chat_message_reports_group_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_message_reports');
        Schema::dropIfExists('content_publish_requests');
    }
};
