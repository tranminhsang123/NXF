<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->unsignedBigInteger('reply_to_message_id')
                ->nullable()
                ->after('edited_at');

            $table->index('reply_to_message_id', 'chat_messages_reply_to_idx');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('chat_messages_reply_to_idx');
            $table->dropColumn('reply_to_message_id');
        });
    }
};
