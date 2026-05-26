<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 50);
            $table->string('message', 255);
            $table->json('context')->nullable();
            $table->timestamps();
        });

        // Thêm các cấu hình bảo mật nâng cao (nếu chưa tồn tại)
        $now = now();
        $rows = [
            ['key' => 'devtools_lock_message', 'value' => 'Tài khoản của bạn đã bị khóa do vi phạm quy định sử dụng công cụ nhà phát triển. Vui lòng liên hệ quản trị viên.', 'created_at' => $now, 'updated_at' => $now],
            ['key' => 'devtools_auto_unlock_hours', 'value' => '0', 'created_at' => $now, 'updated_at' => $now],
        ];

        foreach ($rows as $row) {
            if (! DB::table('security_settings')->where('key', $row['key'])->exists()) {
                DB::table('security_settings')->insert($row);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('system_logs');
        DB::table('security_settings')->whereIn('key', [
            'devtools_lock_message',
            'devtools_auto_unlock_hours',
        ])->delete();
    }
};

