<?php

/**
 * Map Laravel route name → một permission slug (module.action).
 * Middleware admin.route.permission dùng file này.
 */
return [
    'routes' => [
        'admin.dashboard' => 'dashboard.view',

        'admin.notifications.index' => 'notifications.view',
        'admin.notifications.mark-read' => 'notifications.view',
        'admin.notifications.mark-all-read' => 'notifications.view',

        'admin.inbox.index' => 'inbox.view',
        'admin.inbox.unread-count' => 'inbox.view',
        'admin.inbox.show' => 'inbox.view',
        'admin.inbox.messages.fetch' => 'inbox.view',
        'admin.inbox.messages.store' => 'inbox.reply',

        'admin.alphabets.index' => 'alphabets.view',
        'admin.alphabets.show' => 'alphabets.view',
        'admin.alphabets.create' => 'alphabets.edit',
        'admin.alphabets.store' => 'alphabets.edit',
        'admin.alphabets.edit' => 'alphabets.edit',
        'admin.alphabets.update' => 'alphabets.edit',
        'admin.alphabets.destroy' => 'alphabets.delete',

        'admin.kanjis.index' => 'kanjis.view',
        'admin.kanjis.show' => 'kanjis.view',
        'admin.kanjis.create' => 'kanjis.edit',
        'admin.kanjis.store' => 'kanjis.edit',
        'admin.kanjis.edit' => 'kanjis.edit',
        'admin.kanjis.update' => 'kanjis.edit',
        'admin.kanjis.destroy' => 'kanjis.delete',

        'admin.minna.index' => 'minna.view',
        'admin.minna.show' => 'minna.view',
        'admin.minna.create' => 'minna.edit',
        'admin.minna.store' => 'minna.edit',
        'admin.minna.edit' => 'minna.edit',
        'admin.minna.update' => 'minna.edit',
        'admin.minna.destroy' => 'minna.delete',
        'admin.minna.add-sections' => 'minna.edit',
        'admin.minna-section.edit' => 'minna.edit',
        'admin.minna-section.update' => 'minna.edit',

        'admin.course-data.index' => 'course_data.view',
        'admin.course-data.show' => 'course_data.view',
        'admin.course-data.create' => 'course_data.edit',
        'admin.course-data.store' => 'course_data.edit',
        'admin.course-data.edit' => 'course_data.edit',
        'admin.course-data.update' => 'course_data.edit',
        'admin.course-data.duplicate' => 'course_data.edit',
        'admin.course-data.destroy' => 'course_data.delete',

        'admin.chat.groups.index' => 'chat_groups.view',
        'admin.chat.groups.show' => 'chat_groups.view',
        'admin.chat.groups.create' => 'chat_groups.edit',
        'admin.chat.groups.store' => 'chat_groups.edit',
        'admin.chat.groups.edit' => 'chat_groups.edit',
        'admin.chat.groups.update' => 'chat_groups.edit',
        'admin.chat.groups.destroy' => 'chat_groups.delete',
        'admin.chat.groups.join-requests.approve' => 'chat_groups.moderate',
        'admin.chat.groups.join-requests.decline' => 'chat_groups.moderate',

        'admin.users.index' => 'users.view',
        'admin.users.show' => 'users.view',
        'admin.users.edit' => 'users.edit',
        'admin.users.update' => 'users.edit',
        'admin.users.destroy' => 'users.delete',
        'admin.users.lock' => 'users.lock',
        'admin.users.unlock' => 'users.lock',
        'admin.users.admin-roles.update' => 'users.assign_roles',

        'admin.admin-roles.index' => 'admin_roles.view',
        'admin.admin-roles.create' => 'admin_roles.edit',
        'admin.admin-roles.store' => 'admin_roles.edit',
        'admin.admin-roles.edit' => 'admin_roles.edit',
        'admin.admin-roles.update' => 'admin_roles.edit',
        'admin.admin-roles.destroy' => 'admin_roles.delete',

        'admin.security.index' => 'security.view',
        'admin.security.update' => 'security.edit',
        'admin.system-health.index' => 'system_health.view',

        'admin.logo-settings.index' => 'settings.view',
        'admin.logo-settings.store' => 'settings.edit',
        'admin.logo-settings.update' => 'settings.edit',
        'admin.logo-settings.destroy' => 'settings.edit',

        'admin.system-logs.index' => 'system_logs.view',

        'admin.audit-logs.index' => 'audit_logs.view',

        'admin.content-reports.index' => 'content_reports.view',
        'admin.content-reports.show' => 'content_reports.view',
        'admin.content-reports.update' => 'content_reports.manage',

        'admin.content-ops.index' => 'content_ops.view',
        'admin.content-ops.preview' => 'content_ops.view',
        'admin.content-ops.versions' => 'content_ops.view',
        'admin.content-ops.status' => 'content_ops.edit',
        'admin.content-ops.publish-requests.store' => 'content_ops.edit',
        'admin.content-ops.publish-requests.approve' => 'content_ops.edit',
        'admin.content-ops.publish-requests.reject' => 'content_ops.edit',
        'admin.content-ops.restore' => 'content_ops.edit',

        'admin.audio.index' => 'audio.view',
        'admin.audio.generate' => 'audio.manage',
        'admin.audio.bulk-generate' => 'audio.manage',
        'admin.audio.destroy' => 'audio.manage',

        'admin.analytics.index' => 'analytics.view',

        'admin.support-moderation.index' => 'support_moderation.view',
        'admin.support-moderation.reports.dismiss' => 'chat_groups.moderate',
        'admin.support-moderation.reports.remove-message' => 'chat_groups.moderate',

        'admin.growth.index' => 'growth.view',
        'admin.growth.create' => 'growth.edit',
        'admin.growth.store' => 'growth.edit',
        'admin.growth.send' => 'growth.send',
    ],
];
