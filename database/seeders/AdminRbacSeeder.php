<?php

namespace Database\Seeders;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminRbacSeeder extends Seeder
{
    public function run(): void
    {
        $definitions = [
            ['slug' => 'dashboard.view', 'name' => 'Dashboard: xem'],
            ['slug' => 'notifications.view', 'name' => 'Thông báo: xem'],
            ['slug' => 'inbox.view', 'name' => 'Inbox: xem'],
            ['slug' => 'inbox.reply', 'name' => 'Inbox: trả lời'],
            ['slug' => 'alphabets.view', 'name' => 'Bảng chữ cái: xem'],
            ['slug' => 'alphabets.edit', 'name' => 'Bảng chữ cái: tạo/sửa'],
            ['slug' => 'alphabets.delete', 'name' => 'Bảng chữ cái: xóa'],
            ['slug' => 'kanjis.view', 'name' => 'Kanji: xem'],
            ['slug' => 'kanjis.edit', 'name' => 'Kanji: tạo/sửa'],
            ['slug' => 'kanjis.delete', 'name' => 'Kanji: xóa'],
            ['slug' => 'minna.view', 'name' => 'Minna: xem'],
            ['slug' => 'minna.edit', 'name' => 'Minna: tạo/sửa'],
            ['slug' => 'minna.delete', 'name' => 'Minna: xóa'],
            ['slug' => 'course_data.view', 'name' => 'Khóa học JLPT: xem'],
            ['slug' => 'course_data.edit', 'name' => 'Khóa học JLPT: tạo/sửa'],
            ['slug' => 'course_data.delete', 'name' => 'Khóa học JLPT: xóa'],
            ['slug' => 'chat_groups.view', 'name' => 'Nhóm chat: xem'],
            ['slug' => 'chat_groups.edit', 'name' => 'Nhóm chat: tạo/sửa'],
            ['slug' => 'chat_groups.delete', 'name' => 'Nhóm chat: xóa'],
            ['slug' => 'chat_groups.moderate', 'name' => 'Nhóm chat: duyệt tham gia'],
            ['slug' => 'users.view', 'name' => 'Users: xem'],
            ['slug' => 'users.edit', 'name' => 'Users: sửa thông tin'],
            ['slug' => 'users.delete', 'name' => 'Users: xóa'],
            ['slug' => 'users.lock', 'name' => 'Users: khóa/mở khóa'],
            ['slug' => 'users.assign_roles', 'name' => 'Users: gán vai trò admin (RBAC)'],
            ['slug' => 'admin_roles.view', 'name' => 'Vai trò admin: xem'],
            ['slug' => 'admin_roles.edit', 'name' => 'Vai trò admin: tạo/sửa'],
            ['slug' => 'admin_roles.delete', 'name' => 'Vai trò admin: xóa'],
            ['slug' => 'security.view', 'name' => 'Bảo mật: xem'],
            ['slug' => 'security.edit', 'name' => 'Bảo mật: sửa'],
            ['slug' => 'system_health.view', 'name' => 'Sức khỏe hệ thống: xem'],
            ['slug' => 'settings.view', 'name' => 'Cài đặt site: xem'],
            ['slug' => 'settings.edit', 'name' => 'Cài đặt site: sửa'],
            ['slug' => 'system_logs.view', 'name' => 'Log hệ thống: xem'],
            ['slug' => 'audit_logs.view', 'name' => 'Nhật ký admin: xem'],
            ['slug' => 'content_reports.view', 'name' => 'Báo lỗi nội dung: xem'],
            ['slug' => 'content_reports.manage', 'name' => 'Báo lỗi nội dung: xử lý'],
            ['slug' => 'content_ops.view', 'name' => 'Vận hành nội dung: xem'],
            ['slug' => 'content_ops.edit', 'name' => 'Vận hành nội dung: xuất bản/khôi phục'],
            ['slug' => 'audio.view', 'name' => 'Audio/TTS: xem'],
            ['slug' => 'audio.manage', 'name' => 'Audio/TTS: tạo/xóa audio'],
            ['slug' => 'analytics.view', 'name' => 'Phân tích học tập: xem'],
            ['slug' => 'support_moderation.view', 'name' => 'Hỗ trợ/kiểm duyệt: xem'],
            ['slug' => 'growth.view', 'name' => 'Công cụ tăng trưởng: xem'],
            ['slug' => 'growth.edit', 'name' => 'Công cụ tăng trưởng: tạo chiến dịch'],
            ['slug' => 'growth.send', 'name' => 'Công cụ tăng trưởng: gửi chiến dịch'],
        ];

        foreach ($definitions as $row) {
            AdminPermission::query()->updateOrCreate(
                ['slug' => $row['slug']],
                ['name' => $row['name']]
            );
        }

        $allPermissionIds = AdminPermission::query()->pluck('id')->all();

        $super = AdminRole::query()->updateOrCreate(
            ['slug' => 'super_admin'],
            ['name' => 'Super Admin']
        );
        $super->permissions()->sync($allPermissionIds);

        $contentSlugs = [
            'dashboard.view',
            'system_health.view',
            'alphabets.view', 'alphabets.edit', 'alphabets.delete',
            'kanjis.view', 'kanjis.edit', 'kanjis.delete',
            'minna.view', 'minna.edit', 'minna.delete',
            'course_data.view', 'course_data.edit', 'course_data.delete',
            'content_reports.view', 'content_reports.manage',
            'content_ops.view', 'content_ops.edit',
            'audio.view', 'audio.manage',
            'analytics.view',
            'audit_logs.view',
        ];
        $content = AdminRole::query()->updateOrCreate(
            ['slug' => 'content_editor'],
            ['name' => 'Biên tập nội dung']
        );
        $content->permissions()->sync(
            AdminPermission::query()->whereIn('slug', $contentSlugs)->pluck('id')->all()
        );

        $modSlugs = [
            'dashboard.view',
            'notifications.view',
            'inbox.view', 'inbox.reply',
            'chat_groups.view', 'chat_groups.edit', 'chat_groups.delete', 'chat_groups.moderate',
            'support_moderation.view',
            'users.view',
        ];
        $mod = AdminRole::query()->updateOrCreate(
            ['slug' => 'moderator'],
            ['name' => 'Moderator (chat + inbox)']
        );
        $mod->permissions()->sync(
            AdminPermission::query()->whereIn('slug', $modSlugs)->pluck('id')->all()
        );

        $supportSlugs = ['dashboard.view', 'notifications.view', 'inbox.view', 'inbox.reply', 'support_moderation.view'];
        $support = AdminRole::query()->updateOrCreate(
            ['slug' => 'support_staff'],
            ['name' => 'Hỗ trợ (inbox + thông báo)']
        );
        $support->permissions()->sync(
            AdminPermission::query()->whereIn('slug', $supportSlugs)->pluck('id')->all()
        );

        foreach (User::query()->where('role', 'admin')->cursor() as $user) {
            if ($user->adminRoles()->count() === 0) {
                $user->adminRoles()->attach($super->id);
            }
        }
    }
}
