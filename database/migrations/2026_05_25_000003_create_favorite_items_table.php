<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorite_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item_key', 80);
            $table->string('item_type', 32)->default('vocabulary');
            $table->text('front');
            $table->text('back');
            $table->string('source_type', 32)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedInteger('lesson_number')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'item_key'], 'favorite_items_user_key_unique');
            $table->index(['user_id', 'item_type'], 'favorite_items_user_type_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorite_items');
    }
};
