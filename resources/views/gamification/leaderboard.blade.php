<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng xếp hạng</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    @include('layouts.header')

    <main class="pt-24 pb-12 min-h-screen">
        <div class="mx-auto max-w-6xl px-4">
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-red-600">Gamification</p>
                    <h1 class="mt-2 text-3xl font-extrabold text-slate-950">Bảng xếp hạng người học</h1>
                    <p class="mt-2 text-slate-600">Xếp theo XP, streak và số bài Minna đã hoàn thành.</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm shadow-sm">
                    Hạng của bạn: <span class="font-extrabold text-red-600">#{{ $rank }}</span>
                </div>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="grid min-w-[720px] grid-cols-[64px_1fr_120px_120px_120px] gap-3 border-b border-slate-200 bg-slate-100 px-4 py-3 text-xs font-bold uppercase text-slate-500">
                    <span>Hạng</span>
                    <span>Người học</span>
                    <span class="text-right">XP</span>
                    <span class="text-right">Streak</span>
                    <span class="text-right">Bài xong</span>
                </div>
                @forelse($leaders as $index => $leader)
                    <div class="grid min-w-[720px] grid-cols-[64px_1fr_120px_120px_120px] gap-3 border-b border-slate-100 px-4 py-4 text-sm last:border-b-0 {{ $leader->id === $user->id ? 'bg-red-50' : 'bg-white' }}">
                        <span class="font-extrabold text-slate-900">#{{ $index + 1 }}</span>
                        <span class="font-bold text-slate-900">{{ $leader->name }}</span>
                        <span class="text-right font-bold">{{ (int) $leader->xp_total }}</span>
                        <span class="text-right">{{ (int) $leader->current_streak }} ngày</span>
                        <span class="text-right">{{ (int) ($completionCounts[$leader->id] ?? 0) }}</span>
                    </div>
                @empty
                    <div class="px-4 py-8 text-center text-sm text-slate-500">Chưa có dữ liệu xếp hạng.</div>
                @endforelse
            </div>

            <div class="mt-6 flex flex-wrap gap-3">
                <a href="{{ route('achievements.share') }}" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Chia sẻ thành tích</a>
                <a href="{{ route('study-room.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-800 hover:bg-slate-50">Vào phòng học nhóm</a>
            </div>
        </div>
    </main>

    @include('layouts.footer')
</body>
</html>
