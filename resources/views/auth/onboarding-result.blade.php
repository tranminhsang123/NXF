<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kết quả placement test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
</head>
<body class="bg-slate-50 font-sans text-slate-900">
    @include('layouts.header')

    <main class="pt-24 pb-12 min-h-screen">
        <div class="mx-auto max-w-6xl px-4">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                    {{ session('status') }}
                </div>
            @endif

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="grid gap-0 lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="p-6 md:p-8">
                        <p class="text-sm font-bold uppercase tracking-wide text-red-600">Kết quả placement test</p>
                        <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-slate-950 md:text-4xl">
                            Bạn nên bắt đầu ở mức {{ $summary['level_label'] ?? 'mới bắt đầu' }}
                        </h1>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-slate-600">
                            Hệ thống đã dùng điểm placement, mục tiêu JLPT và lý do học để chọn bài ngắn đầu tiên cho bạn.
                        </p>

                        <div class="mt-6 grid gap-3 sm:grid-cols-3">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase text-slate-500">Điểm test</p>
                                <p class="mt-1 text-2xl font-extrabold text-slate-950">
                                    {{ $summary['placement_test_score'] ?? 0 }}/{{ $totalPlacementQuestions }}
                                </p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase text-slate-500">Mục tiêu</p>
                                <p class="mt-1 text-2xl font-extrabold text-slate-950">{{ $summary['jlpt_goal_label'] ?? 'JLPT N5' }}</p>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase text-slate-500">Thời lượng</p>
                                <p class="mt-1 text-2xl font-extrabold text-slate-950">{{ $summary['daily_study_minutes'] ?? 20 }} phút</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-2">
                            @forelse(($summary['learning_reason_labels'] ?? []) as $label)
                                <span class="rounded-full bg-amber-50 px-3 py-1 text-sm font-semibold text-amber-800">{{ $label }}</span>
                            @empty
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">Thi JLPT</span>
                            @endforelse
                        </div>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ $quickWinUrl }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-3 text-sm font-bold text-white hover:bg-red-700">
                                Bắt đầu bài 5 phút
                            </a>
                            <a href="{{ route('user.dashboard') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 bg-white px-5 py-3 text-sm font-bold text-slate-800 hover:bg-slate-50">
                                Vào dashboard
                            </a>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 bg-slate-950 p-6 text-white lg:border-l lg:border-t-0 md:p-8">
                        <p class="text-sm font-bold uppercase tracking-wide text-red-200">Bài đầu tiên của bạn</p>
                        <h2 class="mt-3 text-2xl font-extrabold">{{ $reasonFocus['mini_lesson']['title'] ?? 'Bài 5 phút đầu tiên' }}</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-300">{{ $reasonFocus['focus_text'] ?? 'Bắt đầu bằng bài ngắn để có cảm giác hoàn thành ngay.' }}</p>
                        <ol class="mt-6 space-y-3">
                            @foreach(($reasonFocus['mini_lesson']['steps'] ?? []) as $index => $step)
                                <li class="flex gap-3 rounded-xl bg-white/10 p-3">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-sm font-extrabold text-slate-950">{{ $index + 1 }}</span>
                                    <span class="text-sm font-semibold text-slate-100">{{ $step }}</span>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </section>

            <section class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-extrabold text-slate-950">Chi tiết theo cấp độ</h2>
                    <div class="mt-5 space-y-4">
                        @foreach($placementBreakdown as $group)
                            <div>
                                <div class="mb-2 flex items-center justify-between gap-3 text-sm">
                                    <span class="font-bold text-slate-800">{{ $group['label'] }}</span>
                                    <span class="text-slate-500">{{ $group['score'] }}/{{ $group['total'] }} đúng</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-100">
                                    <div class="h-2 rounded-full bg-red-600" style="width: {{ $group['percent'] }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-xl font-extrabold text-slate-950">Từ vựng đúng mục tiêu</h2>
                    <div class="mt-4 space-y-3">
                        @foreach(array_slice($reasonFocus['vocabulary'] ?? [], 0, 4) as $word)
                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="font-bold text-slate-950">{{ $word['jp'] }} <span class="text-sm font-medium text-slate-500">{{ $word['reading'] }}</span></p>
                                <p class="text-sm text-slate-600">{{ $word['meaning'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>
    </main>

    @include('layouts.footer')
</body>
</html>
