<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Quản lý hệ thống</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: #f6f7fb;
            color: #111827;
            text-rendering: optimizeLegibility;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .admin-page-shell {
            width: 100%;
            max-width: 1600px;
            margin: 0 auto;
        }

        .admin-sidebar-scroll a {
            min-height: 42px;
            margin: 0 12px 4px;
            padding: 10px 14px !important;
            border-left-width: 0 !important;
            border-radius: 10px;
            gap: 10px;
            font-size: 14px;
            line-height: 1.25;
            transition: background-color .15s ease, color .15s ease, transform .15s ease, box-shadow .15s ease;
        }

        .admin-sidebar-scroll a:hover {
            transform: translateX(2px);
        }

        .admin-sidebar-scroll a > .mr-3,
        .admin-sidebar-scroll a > svg.mr-3 {
            margin-right: 0 !important;
        }

        .admin-sidebar-scroll a[class*="bg-red-600"] {
            background: linear-gradient(135deg, #dc2626, #b91c1c) !important;
            color: #fff !important;
            box-shadow: 0 10px 24px rgba(185, 28, 28, .22);
        }

        .admin-sidebar-scroll p {
            padding-left: 16px !important;
            padding-right: 16px !important;
            letter-spacing: .08em;
        }

        .admin-card {
            border: 1px solid rgba(226, 232, 240, .9);
            background: rgba(255, 255, 255, .96);
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
        }

        .admin-card-hover {
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }

        .admin-card-hover:hover {
            transform: translateY(-1px);
            border-color: rgba(203, 213, 225, 1);
            box-shadow: 0 14px 30px rgba(15, 23, 42, .08);
        }

        .admin-icon-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #334155;
            box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
            transition: background-color .15s ease, color .15s ease, border-color .15s ease;
        }

        .admin-icon-button:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        @media (max-width: 767px) {
            .admin-sidebar-scroll a {
                min-height: 44px;
                margin-left: 10px;
                margin-right: 10px;
                padding: 11px 13px !important;
            }
        }

        .admin-sidebar-scroll {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .admin-sidebar-scroll::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        /* ===== Intro full-screen overlay (scoped) ===== */
        .admin-intro-overlay {
            position: fixed;
            inset: 0;
            z-index: 50;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
            background: linear-gradient(-45deg,#0f172a,#111827,#1e293b,#0f172a);
            background-size: 400% 400%;
            animation: adminIntroBgMove 12s ease infinite;
        }

        .admin-intro-root {
            width: 90%;
            max-width: 1300px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 60px;
            color: #fff;
        }

        .admin-intro-image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            animation: adminIntroFadeIn 1.5s ease forwards;
        }

        .admin-intro-image img {
            width: clamp(250px, 40vw, 500px);
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 30px 50px rgba(0,0,0,0.7));
        }

        .admin-intro-text {
            flex: 1;
            max-width: 550px;
            animation: adminIntroSlideIn 1.2s ease forwards;
        }

        .admin-intro-text h1 {
            font-size: clamp(28px, 4vw, 50px);
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .admin-intro-text p {
            opacity: 0.75;
            margin-bottom: 40px;
            font-size: clamp(14px, 1.2vw, 16px);
        }

        .admin-intro-button {
            padding: 14px 50px;
            border-radius: 40px;
            border: none;
            font-weight: 600;
            background: linear-gradient(135deg,#3b82f6,#2563eb);
            color: white;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 10px 25px rgba(59,130,246,0.3);
        }

        .admin-intro-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(59,130,246,0.6);
        }

        .admin-intro-fade-out {
            opacity: 0;
            transform: scale(1.05);
            transition: 0.6s;
        }

        @keyframes adminIntroSlideIn {
            from {
                opacity: 0;
                transform: translateX(-60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes adminIntroFadeIn {
            from {
                opacity: 0;
                transform: translateX(60px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes adminIntroBgMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        @media (max-width: 900px) {
            .admin-intro-root {
                flex-direction: column;
                text-align: center;
                gap: 28px;
                width: min(92%, 560px);
            }

            .admin-intro-text p {
                margin-bottom: 24px;
            }

            .admin-intro-button {
                width: 100%;
                max-width: 280px;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased">
    @if(session('show_admin_intro'))
    <div id="adminIntroModal" class="admin-intro-overlay">
        <div class="admin-intro-root">
            <div class="admin-intro-image">
                <img src="{{ $siteLogoUrl ?? asset('images/logo/yamato.jpg') }}" alt="Admin Image">
            </div>

            <div class="admin-intro-text">
            <h1>Hệ thống quản trị<br>khóa học tiếng Nhật</h1>
                <p>
                    Hệ thống quản trị đào tạo hiện đại.
                    Quản lý bảng chữ cái, Kanji, bài học Minna no Nihongo và dữ liệu khóa học thông minh.
                </p>
                <button type="button" class="admin-intro-button" onclick="enterAdminSystem()">
                    Truy cập quản trị
                </button>
            </div>
        </div>
    </div>

    <script>
        function enterAdminSystem() {
            const modal = document.getElementById('adminIntroModal');
            if (!modal) return;
            modal.classList.add('admin-intro-fade-out');
            setTimeout(() => {
                modal.remove();
            }, 600);
        }
    </script>
    @endif

    <!-- Sidebar -->
    <div class="flex min-h-screen">
        <div id="adminSidebarOverlay" class="fixed inset-0 z-30 hidden bg-slate-950/55 backdrop-blur-sm md:hidden"></div>
        @include('adminlayout.sidebar')

        <!-- Main Content -->
        <div class="flex-1 min-w-0 md:ml-64">
            @include('adminlayout.header')

            <!-- Content -->
            <main class="admin-page-shell px-4 py-5 sm:px-5 md:px-8 md:py-8 lg:px-10">
                @yield('content')
            </main>
        </div>
    </div>
    <script>
        // Chặn chuột phải và các phím tắt DevTools phổ biến trong trang admin
        document.addEventListener('contextmenu', function (event) {
            event.preventDefault();
        });

        document.addEventListener('keydown', function (event) {
            const key = event.key || '';

            // F12
            if (key === 'F12' || event.keyCode === 123) {
                event.preventDefault();
                return false;
            }

            // Ctrl + Shift + I
            if (event.ctrlKey && event.shiftKey && (key.toLowerCase() === 'i' || event.keyCode === 73)) {
                event.preventDefault();
                return false;
            }

            // Ctrl + Shift + J
            if (event.ctrlKey && event.shiftKey && (key.toLowerCase() === 'j' || event.keyCode === 74)) {
                event.preventDefault();
                return false;
            }

            // Ctrl + U
            if (event.ctrlKey && (key.toLowerCase() === 'u' || event.keyCode === 85)) {
                event.preventDefault();
                return false;
            }
        });

        // Global unread badge cho inbox admin
        (function () {
            const unreadUrl = @json(route('admin.inbox.unread-count'));
            const headerBadge = document.getElementById('admin-inbox-unread-badge-header');
            const sidebarBadge = document.getElementById('admin-inbox-unread-badge-sidebar');

            function applyBadge(el, count) {
                if (!el) return;
                if (count > 0) {
                    el.textContent = count > 99 ? '99+' : String(count);
                    el.classList.remove('hidden');
                    el.classList.add('inline-flex');
                } else {
                    el.classList.add('hidden');
                    el.classList.remove('inline-flex');
                }
            }

            async function refreshInboxUnreadCount() {
                try {
                    const response = await fetch(unreadUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    if (!response.ok) return;
                    const data = await response.json();
                    const count = Number(data.unread_count || 0);
                    applyBadge(headerBadge, count);
                    applyBadge(sidebarBadge, count);
                } catch (e) {}
            }

            refreshInboxUnreadCount();
            setInterval(refreshInboxUnreadCount, 5000);
        })();

        // Toggle sidebar for mobile
        (function () {
            const sidebar = document.getElementById('adminSidebar');
            const overlay = document.getElementById('adminSidebarOverlay');
            const toggleButton = document.getElementById('adminSidebarToggle');
            const closeButton = document.getElementById('adminSidebarClose');

            if (!sidebar || !overlay || !toggleButton) return;

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            }

            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }

            toggleButton.addEventListener('click', openSidebar);
            overlay.addEventListener('click', closeSidebar);
            if (closeButton) {
                closeButton.addEventListener('click', closeSidebar);
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    closeSidebar();
                }
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth >= 768) {
                    overlay.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                } else {
                    closeSidebar();
                }
            });
        })();
    </script>
</body>
</html>
