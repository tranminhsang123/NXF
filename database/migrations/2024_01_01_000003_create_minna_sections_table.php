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
        Schema::create('minna_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lesson_id')->constrained('minna_lessons')->onDelete('cascade');
            $table->integer('order_index'); // Thứ tự phần trong bài (1-5)
            $table->string('key'); // Mã phần: tu-vung, ngu-phap, luyen-doc, hoi-thoai, han-tu
            $table->string('title'); // Tiêu đề phần
            $table->json('content')->nullable(); // Nội dung dữ liệu dạng JSON
            $table->string('media_url')->nullable(); // URL media (ảnh, audio, video)
            $table->timestamps();
            
            // Index để tìm nhanh theo lesson và order
            $table->index(['lesson_id', 'order_index']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('minna_sections');
    }
};

