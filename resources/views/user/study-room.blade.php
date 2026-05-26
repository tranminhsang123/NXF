<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Phòng học nhóm</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    @include('layouts.header')

    <main class="pt-24 pb-12 min-h-screen">
        <div class="mx-auto max-w-6xl px-4">
            <div class="mb-6">
                <p class="text-sm font-bold uppercase tracking-wide text-red-600">Cộng đồng</p>
                <h1 class="mt-2 text-3xl font-extrabold text-slate-950">Phòng học nhóm realtime</h1>
                <p class="mt-2 text-slate-600">Vào nhóm chat, học cùng nhau và mở quiz nhanh theo bài đang được gợi ý.</p>
            </div>

            <section class="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-sm font-bold uppercase text-slate-500">Quiz nhóm nhanh</p>
                        <h2 class="mt-1 text-2xl font-extrabold text-slate-950">
                            {{ $nextSection ? 'Bài '.$nextSection['lesson_number'].' - '.$nextSection['section_title'] : 'Chọn bài Minna để bắt đầu' }}
                        </h2>
                        <p class="mt-2 text-sm text-slate-600">Dùng bài được cá nhân hóa làm đề quiz chung, sau đó trao đổi trong nhóm chat.</p>
                    </div>
                    <a href="{{ $nextUrl }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-3 text-sm font-bold text-white hover:bg-red-700">
                        Mở bài quiz nhóm
                    </a>
                </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-extrabold text-slate-950">Nhóm của bạn</h2>
                    <div class="mt-4 space-y-3">
                        @forelse($groups as $group)
                            <a href="{{ route('chat.show', ['group' => $group->id]) }}" class="block rounded-xl border border-slate-200 p-4 hover:border-red-200 hover:bg-red-50">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="font-bold text-slate-950">{{ $group->name }}</p>
                                    <span class="text-xs font-semibold text-slate-500">{{ $group->members_count }} thành viên</span>
                                </div>
                                <p class="mt-1 text-sm text-slate-500">{{ $group->messages_count }} tin nhắn</p>
                            </a>
                        @empty
                            <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">Bạn chưa tham gia nhóm nào. Chọn một nhóm bên dưới để gửi yêu cầu tham gia.</p>
                        @endforelse
                    </div>
                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-extrabold text-slate-950">Nhóm có thể tham gia</h2>
                    <div class="mt-4 space-y-3">
                        @forelse($availableGroups as $group)
                            <div class="rounded-xl border border-slate-200 p-4">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="font-bold text-slate-950">{{ $group->name }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $group->members_count }} thành viên</p>
                                    </div>
                                    <form method="POST" action="{{ route('chat.groups.request-join', ['group' => $group->id]) }}">
                                        @csrf
                                        <button class="rounded-lg bg-slate-900 px-3 py-2 text-sm font-bold text-white hover:bg-slate-700">Xin vào</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <p class="rounded-xl bg-slate-50 p-4 text-sm text-slate-600">Chưa có nhóm mới để tham gia.</p>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </main>

    @include('layouts.footer')
</body>
</html>
