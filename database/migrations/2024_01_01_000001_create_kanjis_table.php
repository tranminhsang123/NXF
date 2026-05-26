<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('kanjis', function (Blueprint $table) {
            $table->id();
            $table->string('character'); // Ký tự Kanji (日)
            $table->text('meaning'); // Nghĩa tiếng Việt (ngày, mặt trời)
            $table->string('on_reading'); // Âm On (ニチ, ジツ)
            $table->string('kun_reading'); // Âm Kun (ひ, び, か)
            $table->string('level'); // Cấp độ JLPT (N5, N4, N3, N2, N1)
            $table->integer('stroke_count'); // Số nét viết
            $table->string('radical'); // Bộ thủ
            $table->text('examples')->nullable(); // Ví dụ sử dụng
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanjis');
    }
};
