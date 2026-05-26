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
        Schema::create('minna_lessons', function (Blueprint $table) {
            $table->id();
            $table->integer('number')->unique(); // Số thứ tự bài (1-50)
            $table->string('title'); // Tiêu đề bài học
            $table->text('description')->nullable(); // Mô tả bài học (nếu có)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minna_lessons');
    }
};

