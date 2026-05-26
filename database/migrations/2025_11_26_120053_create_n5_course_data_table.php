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
        Schema::create('n5_course_data', function (Blueprint $table) {
            $table->id();
            $table->string('section_type'); // speed_master_n5, luyen_doc, marugoto_n5, korede_daijoubu, gokaku_dekiru, tanki_master_n5
            $table->string('section_key')->nullable(); // tuVung, nguPhap, docHieu, ngheHieu
            $table->string('bai')->nullable(); // Bài 1, Bài 2, Mondai 1-1, etc.
            $table->string('title')->nullable(); // Tiêu đề
            $table->json('content'); // Nội dung dữ liệu dạng JSON
            $table->integer('order')->default(0); // Thứ tự sắp xếp
            $table->timestamps();
            
            // Index để tìm nhanh
            $table->index(['section_type', 'section_key']);
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('n5_course_data');
    }
};
