<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logo_settings', function (Blueprint $table) {
            $table->string('logo_title')->nullable()->after('logo_path');
            $table->string('logo_subtitle')->nullable()->after('logo_title');
        });
    }

    public function down(): void
    {
        Schema::table('logo_settings', function (Blueprint $table) {
            $table->dropColumn(['logo_title', 'logo_subtitle']);
        });
    }
};
