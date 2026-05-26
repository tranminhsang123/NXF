<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hoàn thành bài đầu tiên</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    @include('layouts.header')

    <main class="flex min-h-screen items-center pt-24 pb-12">
        <div class="mx-auto max-w-5xl px-4">
            <section class="rounded-2xl border border-slate-200 bg-white p-6 text-center shadow-sm md:p-10">
                <p class="text-sm font-bold uppercase tracking-wide text-red-600">Quick win đầu tiên</p>
                <h1 class="mt-3 text-3xl font-extrabold text-slate-950 md:text-5xl">Bạn đã hoàn thành bài học đầu tiên</h1>
                <p class="mx-auto mt-4 max-w-2xl text-base leading-7 text-slate-600">
                    Đây là mốc quan trọng nhất của ngày đầu: bạn đã biết mình bắt đầu ở đâu và đã có một lần hoàn thành thật.
                </p>

                <div class="mt-8 grid gap-4 md:grid-cols-3">
                    <div class="rounded-xl bg-red-50 p-5">
                        <p class="text-xs font-semibold uppercase text-red-700">XP hiện tại</p>
                        <p class="mt-1 text-3xl font-extrabold text-red-700">{{ (int) ($user->xp_total ?? 0) }}</p>
                    </div>
                    <div class="rounded-xl bg-emerald-50 p-5">
                        <p class="text-xs font-semibold uppercase text-emerald-700">Streak</p>
                        <p class="mt-1 text-3xl font-extrabold text-emerald-700">{{ (int) ($user->current_streak ?? 0) }} ngày</p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-5">
                        <p class="text-xs font-semibold uppercase text-slate-500">Bài vừa học</p>
                        <p class="mt-1 text-3xl font-extrabold text-slate-950">{{ $lessonNumber ? 'Bài '.$lessonNumber : 'Minna' }}</p>
                    </div>
                </div>

                <div class="mt-8 flex flex-col justify-center gap-3 sm:flex-row">
                    <a href="{{ $nextUrl }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-3 text-sm font-bold text-white hover:bg-red-700">
                        Học bài tiếp theo
                    </a>
                    <a href="{{ route('achievements.share') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-800 hover:bg-slate-50">
                        Tạo card chia sẻ
                    </a>
                    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-800 hover:bg-slate-50">
                        Về dashboard
                    </a>
                </div>
            </section>
        </div>
    </main>

    @include('layouts.footer')
</body>
</html>
