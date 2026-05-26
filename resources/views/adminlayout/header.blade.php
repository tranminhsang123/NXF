<!-- Header -->
<header class="sticky top-0 z-20 border-b border-gray-200 bg-white/95 backdrop-blur">
    <div class="mx-auto flex min-h-[3.25rem] max-w-[100vw] items-center justify-between gap-2 px-3 py-2.5 sm:gap-3 sm:px-4 md:min-h-[3.5rem] md:px-8 md:py-3.5">
        <div class="flex min-w-0 flex-1 items-center gap-2.5 sm:gap-3">
            <button
                id="adminSidebarToggle"
                type="button"
                class="md:hidden flex size-10 shrink-0 items-center justify-center rounded-lg border border-gray-200 bg-white text-lg leading-none text-gray-800 shadow-sm hover:bg-gray-50 active:bg-gray-100"
                aria-label="Mở menu"
            >
                <span class="-mt-px" aria-hidden="true">☰</span>
            </button>
            <div class="min-w-0 flex flex-col justify-center gap-0.5">
                <h2 class="truncate text-lg font-bold leading-tight tracking-tight text-gray-900 sm:text-xl md:text-2xl">@yield('admin_title', 'Dashboard')</h2>
                <p class="truncate text-[11px] leading-snug text-gray-500 sm:text-xs md:text-sm">Chào mừng trở lại, {{ auth()->user()->name ?? 'Admin' }}!</p>
            </div>
        </div>

        <div class="flex shrink-0 items-center gap-1.5 sm:gap-2">
            @adminCan('inbox.view')
                <a
                    href="{{ route('admin.inbox.index') }}"
                    class="relative flex size-10 items-center justify-center rounded-lg border border-gray-200/80 bg-gray-50 text-gray-800 hover:bg-gray-100"
                    title="Inbox 1-1"
                >
                    <span class="text-base leading-none" aria-hidden="true">📨</span>
                    <span id="admin-inbox-unread-badge-header" class="absolute -right-0.5 -top-0.5 hidden min-h-[1.125rem] min-w-[1.125rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">0</span>
                </a>
            @endadminCan

            @adminCan('notifications.view')
                <a
                    href="{{ route('admin.notifications.index') }}"
                    class="relative flex size-10 items-center justify-center rounded-lg border border-gray-200/80 bg-gray-50 text-gray-800 hover:bg-gray-100"
                    title="Thông báo"
                >
                    <span class="text-base leading-none" aria-hidden="true">🔔</span>
                    @php $unreadCount = \App\Models\Notification::unreadCountFor(auth()->user()); @endphp
                    @if($unreadCount > 0)
                        <span class="absolute -right-0.5 -top-0.5 flex min-h-[1.125rem] min-w-[1.125rem] items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                    @endif
                </a>
            @endadminCan

            <div class="hidden items-center gap-2 border-l border-gray-200 pl-2 sm:pl-3 md:flex">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-full bg-red-600 text-sm font-bold text-white">
                    {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="max-w-[10rem] lg:max-w-[14rem]">
                    <p class="truncate text-sm font-semibold text-gray-900">{{ auth()->user()->name ?? 'Admin' }}</p>
                    <p class="truncate text-xs text-gray-500">Administrator</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}" class="flex items-center">
                @csrf
                <button
                    type="submit"
                    class="inline-flex h-10 items-center whitespace-nowrap rounded-lg border border-gray-300 bg-white px-3 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 sm:px-3.5 sm:text-sm"
                >
                    Đăng xuất
                </button>
            </form>
        </div>
    </div>
</header>
