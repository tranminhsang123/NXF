<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chia sẻ thành tích</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    @include('layouts.header')

    <main class="pt-24 pb-12 min-h-screen">
        <div class="mx-auto max-w-4xl px-4">
            <h1 class="mb-6 text-3xl font-extrabold text-slate-950">Card chia sẻ thành tích</h1>

            <section class="rounded-2xl bg-slate-950 p-6 text-white shadow-sm md:p-8" id="shareCard">
                <p class="text-sm font-bold uppercase tracking-wide text-red-200">Tiến độ học tiếng Nhật</p>
                <h2 class="mt-3 text-3xl font-extrabold">{{ $user->name }}</h2>
                <div class="mt-8 grid gap-4 sm:grid-cols-3">
                    <div class="rounded-xl bg-white/10 p-4">
                        <p class="text-xs font-semibold uppercase text-slate-300">XP</p>
                        <p class="mt-1 text-3xl font-extrabold">{{ (int) ($user->xp_total ?? 0) }}</p>
                    </div>
                    <div class="rounded-xl bg-white/10 p-4">
                        <p class="text-xs font-semibold uppercase text-slate-300">Streak</p>
                        <p class="mt-1 text-3xl font-extrabold">{{ (int) ($user->current_streak ?? 0) }} ngày</p>
                    </div>
                    <div class="rounded-xl bg-white/10 p-4">
                        <p class="text-xs font-semibold uppercase text-slate-300">Minna</p>
                        <p class="mt-1 text-3xl font-extrabold">{{ $completedLessons }} bài</p>
                    </div>
                </div>
                <p class="mt-8 text-sm text-slate-300">Học đều mỗi ngày, ôn lại đúng lúc, tiến bộ từng bài.</p>
            </section>

            <div class="mt-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <label for="shareText" class="text-sm font-bold text-slate-800">Nội dung chia sẻ</label>
                <textarea id="shareText" class="mt-2 h-24 w-full rounded-lg border border-slate-300 p-3 text-sm" readonly>{{ $shareText }}</textarea>
                <div class="mt-4 flex flex-wrap gap-3">
                    <button type="button" id="copyShare" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Sao chép nội dung</button>
                    <a href="{{ route('leaderboard.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-800 hover:bg-slate-50">Xem bảng xếp hạng</a>
                </div>
                <p id="copyStatus" class="mt-3 text-sm font-semibold text-emerald-700" hidden>Đã sao chép.</p>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('copyShare')?.addEventListener('click', function () {
            var text = document.getElementById('shareText').value;
            navigator.clipboard?.writeText(text).then(function () {
                document.getElementById('copyStatus').hidden = false;
            });
        });
    </script>

    @include('layouts.footer')
</body>
</html>
