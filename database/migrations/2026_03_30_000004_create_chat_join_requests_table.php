<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_join_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('group_id')->constrained('chat_groups')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Chỉ dùng cho luồng admin duyệt
            $table->enum('status', ['pending', 'approved', 'declined'])->default('pending');

            $table->timestamp('decided_at')->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['group_id', 'user_id'], 'chat_join_requests_group_user_uq');
            $table->index(['group_id', 'status'], 'chat_join_requests_group_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_join_requests');
    }
};

