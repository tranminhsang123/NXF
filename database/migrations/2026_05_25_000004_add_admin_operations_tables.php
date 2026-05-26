<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['alphabets', 'kanjis', 'minna_lessons', 'minna_sections', 'n5_course_data'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (! Schema::hasColumn($tableName, 'publish_status')) {
                    $table->string('publish_status', 24)->default('published')->index();
                }
                if (! Schema::hasColumn($tableName, 'published_at')) {
                    $table->timestamp('published_at')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'archived_at')) {
                    $table->timestamp('archived_at')->nullable();
                }
            });
        }

        Schema::create('content_versions', function (Blueprint $table) {
            $table->id();
            $table->string('versionable_type');
            $table->unsignedBigInteger('versionable_id');
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 64);
            $table->json('snapshot');
            $table->json('changes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['versionable_type', 'versionable_id'], 'content_versions_subject_idx');
            $table->index(['actor_id', 'created_at'], 'content_versions_actor_created_idx');
        });

        Schema::create('admin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('auditable_type')->nullable();
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->string('action', 64);
            $table->string('summary', 500);
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 64)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['auditable_type', 'auditable_id'], 'admin_audit_subject_idx');
            $table->index(['actor_id', 'created_at'], 'admin_audit_actor_created_idx');
            $table->index(['action', 'created_at'], 'admin_audit_action_created_idx');
        });

        Schema::create('growth_campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('channel', 32)->default('notification');
            $table->string('segment', 64)->default('all_users');
            $table->string('status', 32)->default('draft')->index();
            $table->unsignedInteger('audience_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('growth_campaigns');
        Schema::dropIfExists('admin_audit_logs');
        Schema::dropIfExists('content_versions');

        foreach (['alphabets', 'kanjis', 'minna_lessons', 'minna_sections', 'n5_course_data'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                foreach (['publish_status', 'published_at', 'archived_at'] as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
