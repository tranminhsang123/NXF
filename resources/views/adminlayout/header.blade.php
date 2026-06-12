<!-- Header -->
<header class="sticky top-0 z-20 border-b border-slate-200/80 bg-white/90 backdrop-blur-xl">
    <div class="flex min-h-[60px] items-center justify-between gap-3 px-3 py-2 sm:px-5 md:min-h-[68px] md:px-8">
        <div class="flex min-w-0 flex-1 items-center gap-2.5 sm:gap-3">
            <button
                id="adminSidebarToggle"
                type="button"
                class="admin-icon-button md:hidden"
                aria-label="Mở menu"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true">
                    <path d="M4 7h16"></path>
                    <path d="M4 12h16"></path>
                    <path d="M4 17h16"></path>
                </svg>
            </button>

            <div class="min-w-0">
                <div class="flex min-w-0 items-center gap-2">
                    <h2 class="truncate text-base font-bold leading-tight text-slate-950 sm:text-lg md:text-xl">
                        @yield('admin_title', 'Dashboard')
                    </h2>
                    <span class="hidden rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600 sm:inline-flex">Admin</span>
                </div>
                <p class="hidden truncate text-xs leading-snug text-slate-500 sm:block md:text-sm">
                    Chào mừng trở lại, {{ auth()->user()->name ?? 'Admin' }}!
                </p>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
            @adminCan('inbox.view')
                <a
                    href="{{ route('admin.inbox.index') }}"
                    class="admin-icon-button relative"
                    title="Inbox 1-1"
                    aria-label="Inbox 1-1"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M4 4h16v16H4z"></path>
                        <path d="M4 13h4l2 3h4l2-3h4"></path>
                    </svg>
                    <span id="admin-inbox-unread-badge-header" class="absolute -right-0.5 -top-0.5 hidden min-h-[1.125rem] min-w-[1.125rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">0</span>
                </a>
            @endadminCan

            @adminCan('notifications.view')
                <a
                    href="{{ route('admin.notifications.index') }}"
                    class="admin-icon-button relative"
                    title="Thông báo"
                    aria-label="Thông báo"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9"></path>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                    </svg>
                    @php $unreadCount = \App\Models\Notification::unreadCountFor(auth()->user()); @endphp
                    @if($unreadCount > 0)
                        <span class="absolute -right-0.5 -top-0.5 flex min-h-[1.125rem] min-w-[1.125rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </a>
            @endadminCan

            <div class="hidden items-center gap-2 border-l border-slate-200 pl-3 lg:flex">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-950 text-sm font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="max-w-[12rem]">
                    <p class="truncate text-sm font-semibold text-slate-950">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="truncate text-xs text-slate-500">Administrator</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                @csrf
                <button
                    type="submit"
                    class="inline-flex h-10 items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-2.5 text-xs font-semibold text-slate-700 shadow-sm hover:border-slate-400 hover:bg-slate-50 sm:px-3.5 sm:text-sm"
                    aria-label="Đăng xuất"
                    title="Đăng xuất"
                >
                    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <path d="M16 17l5-5-5-5"></path>
                        <path d="M21 12H9"></path>
                    </svg>
                    <span class="hidden sm:inline">Đăng xuất</span>
                </button>
            </form>
        </div>
    </div>
</header>
