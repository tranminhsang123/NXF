<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('growth_campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('growth_campaign_id')->constrained('growth_campaigns')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('notification_id')->nullable()->constrained('notifications')->nullOnDelete();
            $table->string('variant', 8)->default('a');
            $table->string('channel', 32);
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('notification_sent_at')->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamps();

            $table->unique(['growth_campaign_id', 'user_id'], 'growth_campaign_user_unique');
            $table->index(['growth_campaign_id', 'variant'], 'growth_campaign_variant_idx');
            $table->index(['user_id', 'sent_at'], 'growth_campaign_recipient_user_sent_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('growth_campaign_recipients');
    }
};
