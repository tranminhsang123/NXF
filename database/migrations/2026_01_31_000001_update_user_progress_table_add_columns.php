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
        if (Schema::hasTable('user_progresses')) {
            return;
        }

        if (! Schema::hasTable('user_progress')) {
            Schema::create('user_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('lesson_type', 50);
                $table->unsignedBigInteger('lesson_id');
                $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
                $table->timestamp('last_accessed_at')->nullable();
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'lesson_type', 'lesson_id'], 'user_lesson_unique');
                $table->index(['lesson_type', 'lesson_id'], 'lesson_lookup_index');
            });

            return;
        }

        Schema::table('user_progress', function (Blueprint $table) {
            if (! Schema::hasColumn('user_progress', 'lesson_type')) {
                $table->string('lesson_type', 50)->after('user_id');
            }

            if (! Schema::hasColumn('user_progress', 'lesson_id')) {
                $table->unsignedBigInteger('lesson_id')->after('lesson_type');
            }

            if (! Schema::hasColumn('user_progress', 'status')) {
                $table->enum('status', ['in_progress', 'completed'])
                    ->default('in_progress')
                    ->after('lesson_id');
            }

            if (! Schema::hasColumn('user_progress', 'last_accessed_at')) {
                $table->timestamp('last_accessed_at')->nullable()->after('status');
            }

            if (! Schema::hasColumn('user_progress', 'completed_at')) {
                $table->timestamp('completed_at')->nullable()->after('last_accessed_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('user_progress')) {
            return;
        }

        // Không drop cả bảng để tránh mất dữ liệu, chỉ minh họa cách revert đơn giản.
        Schema::table('user_progress', function (Blueprint $table) {
            if (Schema::hasColumn('user_progress', 'completed_at')) {
                $table->dropColumn('completed_at');
            }
            if (Schema::hasColumn('user_progress', 'last_accessed_at')) {
                $table->dropColumn('last_accessed_at');
            }
            if (Schema::hasColumn('user_progress', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('user_progress', 'lesson_id')) {
                $table->dropColumn('lesson_id');
            }
            if (Schema::hasColumn('user_progress', 'lesson_type')) {
                $table->dropColumn('lesson_type');
            }
        });
    }
};
