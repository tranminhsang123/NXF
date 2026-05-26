<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->uuid('message_uuid')->nullable()->after('id');
            $table->string('client_message_id', 100)->nullable()->after('content');
            $table->uuid('event_id')->nullable()->after('client_message_id');
            $table->uuid('parent_event_id')->nullable()->after('event_id');
            $table->string('event_status', 20)->default('pending')->after('parent_event_id');
            $table->unsignedTinyInteger('event_retry_count')->default(0)->after('event_status');

            $table->unique(['sender_id', 'client_message_id'], 'chat_messages_sender_client_msg_unique');
            $table->index(['event_status', 'created_at'], 'chat_messages_event_status_idx');
        });

        Schema::table('direct_messages', function (Blueprint $table) {
            $table->uuid('message_uuid')->nullable()->after('id');
            $table->string('client_message_id', 100)->nullable()->after('content');
            $table->uuid('event_id')->nullable()->after('client_message_id');
            $table->uuid('parent_event_id')->nullable()->after('event_id');
            $table->string('event_status', 20)->default('pending')->after('parent_event_id');
            $table->unsignedTinyInteger('event_retry_count')->default(0)->after('event_status');

            $table->unique(['sender_id', 'client_message_id'], 'direct_messages_sender_client_msg_unique');
            $table->index(['event_status', 'created_at'], 'direct_messages_event_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropUnique('chat_messages_sender_client_msg_unique');
            $table->dropIndex('chat_messages_event_status_idx');
            $table->dropColumn([
                'message_uuid',
                'client_message_id',
                'event_id',
                'parent_event_id',
                'event_status',
                'event_retry_count',
            ]);
        });

        Schema::table('direct_messages', function (Blueprint $table) {
            $table->dropUnique('direct_messages_sender_client_msg_unique');
            $table->dropIndex('direct_messages_event_status_idx');
            $table->dropColumn([
                'message_uuid',
                'client_message_id',
                'event_id',
                'parent_event_id',
                'event_status',
                'event_retry_count',
            ]);
        });
    }
};
