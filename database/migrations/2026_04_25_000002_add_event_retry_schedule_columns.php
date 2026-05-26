<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->timestamp('next_retry_at')->nullable()->after('event_retry_count');
            $table->text('event_last_error')->nullable()->after('next_retry_at');
            $table->index(['event_status', 'next_retry_at'], 'chat_messages_event_retry_idx');
        });

        Schema::table('direct_messages', function (Blueprint $table) {
            $table->timestamp('next_retry_at')->nullable()->after('event_retry_count');
            $table->text('event_last_error')->nullable()->after('next_retry_at');
            $table->index(['event_status', 'next_retry_at'], 'direct_messages_event_retry_idx');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('chat_messages_event_retry_idx');
            $table->dropColumn(['next_retry_at', 'event_last_error']);
        });

        Schema::table('direct_messages', function (Blueprint $table) {
            $table->dropIndex('direct_messages_event_retry_idx');
            $table->dropColumn(['next_retry_at', 'event_last_error']);
        });
    }
};
