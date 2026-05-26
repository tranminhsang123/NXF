<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('flashcard_card_states', function (Blueprint $table) {
            $table->unsignedInteger('lapses')->default(0)->after('last_quality');
        });
    }

    public function down(): void
    {
        Schema::table('flashcard_card_states', function (Blueprint $table) {
            $table->dropColumn('lapses');
        });
    }
};
