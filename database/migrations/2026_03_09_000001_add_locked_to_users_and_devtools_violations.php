<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('locked_at')->nullable()->after('role');
            $table->string('locked_reason', 500)->nullable()->after('locked_at');
        });

        Schema::create('devtools_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('violation_type', 50); // f12, ctrl_shift_i, ctrl_shift_j, ctrl_u
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });

        Schema::create('security_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Mặc định: ghi log bật, khóa sau 1 lần vi phạm, cửa sổ 24 giờ
        DB::table('security_settings')->insert([
            ['key' => 'devtools_log_enabled', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'devtools_lock_after_violations', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'devtools_violation_window_hours', 'value' => '24', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['locked_at', 'locked_reason']);
        });
        Schema::dropIfExists('devtools_violations');
        Schema::dropIfExists('security_settings');
    }
};
