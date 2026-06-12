<!-- Sidebar -->
<aside id="adminSidebar" class="fixed inset-y-0 left-0 z-40 flex min-h-screen w-[18rem] max-w-[86vw] -translate-x-full transform flex-col bg-slate-950 text-white shadow-2xl transition-transform duration-200 ease-out md:w-64 md:translate-x-0">
    <div class="shrink-0 border-b border-white/10 p-4">
        <div class="flex items-center justify-between">
            <h1 class="flex min-w-0 items-center gap-3 text-base font-bold tracking-tight">
                <img
                    src="{{ $siteLogoUrl ?? asset('images/logo/yamato.jpg') }}"
                    alt="Logo admin"
                    class="h-10 w-10 rounded-lg object-cover ring-1 ring-white/20"
                >
                <span class="truncate leading-none whitespace-nowrap">日本語 Admin</span>
            </h1>
            <button id="adminSidebarClose" type="button" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-white/10 bg-white/5 text-gray-200 hover:bg-white/10 md:hidden" aria-label="Đóng menu">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                    <path d="M6 6l12 12"></path>
                    <path d="M18 6L6 18"></path>
                </svg>
            </button>
        </div>
        <p class="mt-1 text-sm text-slate-400">Hệ thống quản lý</p>
    </div>

    <nav class="admin-sidebar-scroll mt-3 min-h-0 flex-1 overflow-y-auto py-2">
        <p class="px-6 py-2 text-[11px] uppercase tracking-wider text-gray-500 font-semibold">Tổng quan</p>
        @adminCan('dashboard.view')
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.dashboard') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <span class="mr-3">📊</span>
            Tổng quan
        </a>
        @endadminCan

        @adminCan('analytics.view')
        <a href="{{ route('admin.analytics.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.analytics.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 3v18h18"></path>
                <path d="M7 15l4-4 3 3 5-7"></path>
                <path d="M19 7h-4"></path>
                <path d="M19 7v4"></path>
            </svg>
            Phân tích học tập
        </a>
        @endadminCan

        @adminCan('notifications.view')
        <a href="{{ route('admin.notifications.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.notifications.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <span class="mr-3">🔔</span>
            Thông báo
        </a>
        @endadminCan

        @adminCan('inbox.view')
        <a href="{{ route('admin.inbox.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.inbox.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <span class="mr-3">📨</span>
            <span>Hộp thư 1-1</span>
            <span id="admin-inbox-unread-badge-sidebar"
                  class="hidden ml-auto min-w-[18px] h-[18px] px-1 rounded-full bg-red-500 text-white text-[10px] font-bold items-center justify-center">0</span>
        </a>
        @endadminCan

        @adminCan('support_moderation.view')
        <a href="{{ route('admin.support-moderation.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.support-moderation.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 13a8 8 0 0 1 16 0"></path>
                <path d="M4 13v3a2 2 0 0 0 2 2h1v-7H6a2 2 0 0 0-2 2Z"></path>
                <path d="M20 13v3a2 2 0 0 1-2 2h-1v-7h1a2 2 0 0 1 2 2Z"></path>
                <path d="M16 19c-.8 1.2-2.1 2-4 2"></path>
            </svg>
            Hỗ trợ / kiểm duyệt
        </a>
        @endadminCan

        <p class="px-6 py-2 mt-3 text-[11px] uppercase tracking-wider text-gray-500 font-semibold">Nội dung học</p>
        @adminCan('content_ops.view')
        <a href="{{ route('admin.content-ops.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.content-ops.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M8 4h8"></path>
                <path d="M9 2h6a1 1 0 0 1 1 1v2H8V3a1 1 0 0 1 1-1Z"></path>
                <path d="M6 5h12a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2Z"></path>
                <path d="M8 12h8"></path>
                <path d="M8 16h5"></path>
            </svg>
            Vận hành nội dung
        </a>
        @endadminCan

        @adminCan('content_ops.view')
        <a href="{{ route('admin.content-studio.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.content-studio.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 20h9"></path>
                <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
                <path d="M15 5l3 3"></path>
            </svg>
            Xưởng nội dung
        </a>
        @endadminCan

        @adminCan('content_reports.view')
        <a href="{{ route('admin.content-reports.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.content-reports.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
                <path d="M12 9v4"></path>
                <path d="M12 17h.01"></path>
            </svg>
            Báo lỗi nội dung
        </a>
        @endadminCan
        @adminCan('alphabets.view')
        <a href="{{ route('admin.alphabets.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.alphabets.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <span class="mr-3">🔤</span>
            Quản lý bảng chữ cái
        </a>
        @endadminCan

        @adminCan('kanjis.view')
        <a href="{{ route('admin.kanjis.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.kanjis.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <span class="mr-3">🈶</span>
            Quản lý Kanji
        </a>
        @endadminCan

        @adminCan('minna.view')
        <a href="{{ route('admin.minna.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.minna.*') || request()->routeIs('admin.minna-section.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <span class="mr-3">📚</span>
            Quản lý Minna no Nihongo
        </a>
        @endadminCan

        @adminCan('chat_groups.view')
        <a href="{{ route('admin.chat.groups.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.chat.groups.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <span class="mr-3">💬</span>
            Nhóm chat
        </a>
        @endadminCan

        @adminCan('course_data.view')
        <a href="{{ route('admin.course-data.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.course-data.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <span class="mr-3">🎯</span>
            Quản lý Khóa học JLPT
        </a>
        @endadminCan

        @adminCan('audio.view')
        <a href="{{ route('admin.audio.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.audio.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M11 5 6 9H3v6h3l5 4V5Z"></path>
                <path d="M16 9a5 5 0 0 1 0 6"></path>
                <path d="M19 6a9 9 0 0 1 0 12"></path>
            </svg>
            Quản lý Audio/TTS
        </a>
        @endadminCan

        <p class="px-6 py-2 mt-3 text-[11px] uppercase tracking-wider text-gray-500 font-semibold">Hệ thống</p>
        @adminCan('users.view')
        <a href="{{ route('admin.users.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.users.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <span class="mr-3">👥</span>
            Quản lý người dùng
        </a>
        @endadminCan

        @adminCan('admin_roles.view')
        <a href="{{ route('admin.admin-roles.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.admin-roles.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                <path d="M19 8l2 2 3-4"></path>
            </svg>
            Vai trò admin
        </a>
        @endadminCan

        @adminCan('security.view')
        <a href="{{ route('admin.security.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.security.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <span class="mr-3">🔒</span>
            Bảo mật / DevTools
        </a>
        @endadminCan
        @adminCan('system_health.view')
        <a href="{{ route('admin.system-health.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.system-health.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
            </svg>
            Sức khỏe hệ thống
        </a>
        @endadminCan
        @adminCan('growth.view')
        <a href="{{ route('admin.growth.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.growth.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 17l6-6 4 4 7-8"></path>
                <path d="M14 7h6v6"></path>
            </svg>
            Công cụ tăng trưởng
        </a>
        @endadminCan
        @adminCan('settings.view')
        <a href="{{ route('admin.logo-settings.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.logo-settings.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <span class="mr-3">🖼️</span>
            Cài đặt
        </a>
        @endadminCan
        @adminCan('system_logs.view')
        <a href="{{ route('admin.system-logs.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.system-logs.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <span class="mr-3">📋</span>
            Log hệ thống
        </a>
        @endadminCan
        @adminCan('audit_logs.view')
        <a href="{{ route('admin.audit-logs.index') }}"
           class="flex items-center px-6 py-3 {{ request()->routeIs('admin.audit-logs.*') ? 'bg-red-600/90 border-l-4 border-red-400' : 'text-gray-300 hover:bg-gray-800 hover:text-white' }}">
            <svg class="mr-3 h-5 w-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M8 3h8l4 4v14H8a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4Z"></path>
                <path d="M16 3v5h5"></path>
                <path d="M8 11h8"></path>
                <path d="M8 15h6"></path>
            </svg>
            Nhật ký admin
        </a>
        @endadminCan
    </nav>

    <div class="shrink-0 border-t border-gray-700/80 p-4">
        <a href="{{ route('home') }}"
           class="flex items-center justify-center gap-2 w-full px-4 py-3 rounded-lg border border-gray-600 text-gray-200 text-sm font-semibold hover:bg-gray-800 hover:text-white hover:border-gray-500 transition">
            <span aria-hidden="true">🏠</span>
            Trang chủ
        </a>
    </div>
</aside>
