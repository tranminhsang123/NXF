<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Index cho dashboard: filter user_id + lesson_type, sort last_accessed_at.
     */
    public function up(): void
    {
        Schema::table('user_progresses', function (Blueprint $table) {
            $table->index(['user_id', 'lesson_type', 'last_accessed_at'], 'user_progresses_dashboard_index');
        });
    }

    public function down(): void
    {
        Schema::table('user_progresses', function (Blueprint $table) {
            $table->dropIndex('user_progresses_dashboard_index');
        });
    }
};
