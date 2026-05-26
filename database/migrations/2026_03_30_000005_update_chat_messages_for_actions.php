<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->timestamp('edited_at')->nullable()->after('content');

            // Forward metadata (optional)
            $table->unsignedBigInteger('forwarded_from_message_id')->nullable()->after('edited_at');
            $table->unsignedBigInteger('forwarded_from_group_id')->nullable()->after('forwarded_from_message_id');
            $table->string('forwarded_from_sender_name')->nullable()->after('forwarded_from_group_id');

            $table->softDeletes();

            $table->index(['group_id', 'deleted_at'], 'chat_messages_group_deleted_idx');
        });
    }

    public function down(): void
    {
        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropIndex('chat_messages_group_deleted_idx');
            $table->dropSoftDeletes();
            $table->dropColumn([
                'edited_at',
                'forwarded_from_message_id',
                'forwarded_from_group_id',
                'forwarded_from_sender_name',
            ]);
        });
    }
};

