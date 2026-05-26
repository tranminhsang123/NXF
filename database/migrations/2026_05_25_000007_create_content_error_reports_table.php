<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_error_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('content_type', 64)->nullable();
            $table->unsignedBigInteger('content_id')->nullable();
            $table->string('content_title')->nullable();
            $table->string('category', 32)->default('other');
            $table->string('status', 32)->default('pending')->index();
            $table->string('page_url', 1000)->nullable();
            $table->text('selected_text')->nullable();
            $table->text('description');
            $table->json('browser_context')->nullable();
            $table->text('resolution_note')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['content_type', 'content_id'], 'content_error_reports_subject_idx');
            $table->index(['category', 'status'], 'content_error_reports_category_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_error_reports');
    }
};
