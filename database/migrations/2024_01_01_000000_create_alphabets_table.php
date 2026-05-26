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
        Schema::create('alphabets', function (Blueprint $table) {
            $table->id();
            $table->string('character'); // Ký tự tiếng Nhật (あ, い, う...)
            $table->string('romaji'); // Cách đọc Latin (a, i, u...)
            $table->enum('type', ['hiragana', 'katakana', 'romaji']); // Loại bảng chữ
            $table->string('category')->nullable(); // Phân loại âm (seion, dakuon, yoon)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alphabets');
    }
};
