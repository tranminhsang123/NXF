<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Thêm indexes để tăng tốc queries
     */
    public function up(): void
    {
        // Indexes cho bảng alphabets
        Schema::table('alphabets', function (Blueprint $table) {
            // Index cho type - dùng trong WHERE type = 'hiragana'
            $table->index('type', 'alphabets_type_index');
            
            // Index cho category - dùng trong WHERE category = 'seion'
            $table->index('category', 'alphabets_category_index');
            
            // Composite index cho type và character - tối ưu query kết hợp
            $table->index(['type', 'character'], 'alphabets_type_character_index');
        });

        // Indexes cho bảng kanjis
        Schema::table('kanjis', function (Blueprint $table) {
            // Index cho level - dùng trong WHERE level = 'N5'
            $table->index('level', 'kanjis_level_index');
            
            // Index cho character - dùng trong WHERE character = '日'
            $table->index('character', 'kanjis_character_index');
        });

        // Indexes cho bảng minna_sections
        Schema::table('minna_sections', function (Blueprint $table) {
            // Index cho key - dùng trong WHERE key = 'tu-vung'
            $table->index('key', 'minna_sections_key_index');
            
            // Composite index cho lesson_id và key - tối ưu query kết hợp
            $table->index(['lesson_id', 'key'], 'minna_sections_lesson_key_index');
        });
    }

    /**
     * Reverse the migrations.
     * Xóa indexes khi rollback
     */
    public function down(): void
    {
        Schema::table('alphabets', function (Blueprint $table) {
            $table->dropIndex('alphabets_type_index');
            $table->dropIndex('alphabets_category_index');
            $table->dropIndex('alphabets_type_character_index');
        });

        Schema::table('kanjis', function (Blueprint $table) {
            $table->dropIndex('kanjis_level_index');
            $table->dropIndex('kanjis_character_index');
        });

        Schema::table('minna_sections', function (Blueprint $table) {
            $table->dropIndex('minna_sections_key_index');
            $table->dropIndex('minna_sections_lesson_key_index');
        });
    }
};
