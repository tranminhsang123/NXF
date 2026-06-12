<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flashcard - Ôn từ vựng Minna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .jp {
            font-family: 'Hiragino Sans', 'Yu Gothic', 'Meiryo', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    @include('layouts.header')

    @php
        $lessonCount = $lessonsWithCount->count();
        $totalCards = $lessonsWithCount->sum('count');
        $dueCount = $srsDashboard['due_count'] ?? 0;
        $weakCount = $srsDashboard['weak_count'] ?? 0;
        $upcomingCount = $srsDashboard['upcoming_count'] ?? 0;
        $reviewedCount = $srsDashboard['reviewed_count'] ?? 0;
    @endphp

    <main class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <a href="{{ route('minna.index') }}" class="inline-flex items-center text-sm font-bold text-slate-600 hover:text-slate-950">
            <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Bài học Minna
        </a>

        <section class="mt-5 grid gap-4 lg:grid-cols-[minmax(0,1fr)_20rem] lg:items-stretch">
            <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <p class="text-sm font-bold text-red-600">Ôn từ vựng</p>
                <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Flashcard</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                    Chọn một bài để ôn nhanh, hoặc gom nhiều bài vào một phiên ôn. Chế độ SRS ưu tiên thẻ đến hạn và thẻ mới.
                </p>

                <div class="mt-5 grid grid-cols-2 gap-2 sm:grid-cols-4">
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Bài</p>
                        <p class="mt-1 text-xl font-black text-slate-950">{{ $lessonCount }}</p>
                    </div>
                    <div class="rounded-lg bg-slate-50 p-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Từ</p>
                        <p class="mt-1 text-xl font-black text-slate-950">{{ $totalCards }}</p>
                    </div>
                    <div class="rounded-lg bg-red-50 p-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-red-600">Đến hạn</p>
                        <p class="mt-1 text-xl font-black text-red-700">{{ $dueCount }}</p>
                    </div>
                    <div class="rounded-lg bg-amber-50 p-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-amber-600">Thẻ yếu</p>
                        <p class="mt-1 text-xl font-black text-amber-700">{{ $weakCount }}</p>
                    </div>
                </div>
            </div>

            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-1">
                <div class="rounded-xl border border-violet-100 bg-violet-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-violet-600">Sắp đến hạn</p>
                    <p class="mt-1 text-2xl font-black text-violet-800">{{ $upcomingCount }}</p>
                    <p class="mt-1 text-sm leading-6 text-violet-700">Trong 24 giờ tới.</p>
                </div>
                <div class="rounded-xl border border-slate-200 bg-white p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Đã ôn</p>
                    <p class="mt-1 text-2xl font-black text-slate-950">{{ $reviewedCount }}</p>
                    <p class="mt-1 text-sm leading-6 text-slate-600">Tổng thẻ đã có lịch ôn.</p>
                </div>
            </div>
        </section>

        @if(session('warning'))
            <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-900">
                {{ session('warning') }}
            </div>
        @endif

        <section class="mt-4 grid gap-4 lg:grid-cols-[minmax(0,1fr)_20rem]">
            <form method="GET" action="{{ route('flashcard.study.multi') }}" id="form-multi" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-red-600">Ôn nhiều bài</p>
                        <h2 class="mt-1 text-xl font-black text-slate-950">Tạo phiên ôn</h2>
                    </div>
                    <span id="selected-count" class="inline-flex w-fit rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">0 bài đã chọn</span>
                </div>

                <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
                    @foreach($lessonsWithCount as $item)
                        <label class="group cursor-pointer">
                            <input type="checkbox" name="bai[]" value="{{ $item['lesson']->number }}" class="peer sr-only">
                            <span class="flex min-h-16 flex-col justify-center rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 transition peer-checked:border-red-300 peer-checked:bg-red-50 group-hover:border-slate-300">
                                <span class="text-sm font-black text-slate-950">Bài {{ str_pad($item['lesson']->number, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="mt-1 text-xs font-semibold text-slate-500">{{ $item['count'] }} từ</span>
                            </span>
                        </label>
                    @endforeach
                </div>

                <div class="mt-4 grid gap-2 sm:grid-cols-[1fr_1fr_auto]">
                    <input type="hidden" name="shuffle" value="1">
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                        Ôn đầy đủ
                    </button>
                    <button type="submit" name="mode" value="srs" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-violet-600 px-4 py-2 text-sm font-bold text-white hover:bg-violet-700">
                        Ôn SRS
                    </button>
                    <button type="button" id="select-all" class="inline-flex min-h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">
                        Chọn tất cả
                    </button>
                </div>
            </form>

            <div class="space-y-4">
                @auth
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                        <p class="text-sm font-black text-emerald-950">Bộ từ yêu thích</p>
                        <p class="mt-1 text-sm leading-6 text-emerald-700">Bạn đã lưu {{ $favoriteCount ?? 0 }} từ/câu để ôn riêng.</p>
                        <a href="{{ route('flashcard.favorites') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                            Ôn từ yêu thích
                        </a>
                    </div>
                @else
                    <div class="rounded-xl border border-slate-200 bg-white p-4">
                        <p class="text-sm font-black text-slate-950">SRS cá nhân</p>
                        <p class="mt-1 text-sm leading-6 text-slate-600">Đăng nhập để lưu lịch ôn, thẻ yếu và từ yêu thích.</p>
                        <a href="{{ route('login') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50">
                            Đăng nhập
                        </a>
                    </div>
                @endauth

                @if(!empty($srsDashboard['weak_lessons']) && $srsDashboard['weak_lessons']->isNotEmpty())
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <p class="text-sm font-black text-amber-950">Bài có thẻ yếu</p>
                        <div class="mt-3 flex flex-wrap gap-2">
                            @foreach($srsDashboard['weak_lessons'] as $weakLesson)
                                <a href="{{ route('flashcard.study', ['number' => $weakLesson->number, 'mode' => 'srs']) }}" class="rounded-lg border border-amber-200 bg-white px-3 py-2 text-sm font-bold text-amber-800 hover:bg-amber-100">
                                    Bài {{ $weakLesson->number }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        @if(!empty($srsDashboard['weak_cards']) && $srsDashboard['weak_cards']->isNotEmpty())
            <section class="mt-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-amber-600">Cần chú ý</p>
                        <h2 class="mt-1 text-xl font-black text-slate-950">Thẻ yếu cụ thể</h2>
                    </div>
                    <span class="text-xs font-semibold text-slate-500">Sắp xếp theo độ khó nhớ</span>
                </div>
                <div class="grid gap-3 md:grid-cols-2">
                    @foreach($srsDashboard['weak_cards'] as $weakCard)
                        <div class="rounded-lg border border-amber-100 bg-amber-50 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="jp break-words text-base font-black text-slate-950">{{ $weakCard['front'] }}</p>
                                    <p class="mt-1 break-words text-sm leading-6 text-slate-600">{{ $weakCard['back'] }}</p>
                                </div>
                                @if($weakCard['lesson_number'])
                                    <span class="shrink-0 rounded-full bg-white px-2 py-1 text-xs font-bold text-amber-800">Bài {{ $weakCard['lesson_number'] }}</span>
                                @endif
                            </div>
                            <p class="mt-2 text-xs font-semibold text-slate-500">Mức nhớ {{ $weakCard['last_quality'] ?? '-' }} - EF {{ $weakCard['ease_factor'] ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="mt-6">
            <div class="mb-4 flex items-end justify-between gap-3">
                <div>
                    <p class="text-sm font-bold text-red-600">Ôn nhanh</p>
                    <h2 class="mt-1 text-xl font-black text-slate-950">Chọn một bài</h2>
                </div>
            </div>

            <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                @forelse($lessonsWithCount as $item)
                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-red-200 hover:shadow-md">
                        <a href="{{ route('flashcard.study', $item['lesson']->number) }}" class="block">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-lg font-black text-red-600">Bài {{ str_pad($item['lesson']->number, 2, '0', STR_PAD_LEFT) }}</p>
                                    <p class="mt-1 rounded-full bg-red-50 px-2 py-0.5 text-xs font-bold text-red-700">{{ $item['count'] }} từ</p>
                                </div>
                                <svg class="mt-1 h-5 w-5 shrink-0 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                            <h3 class="jp mt-3 break-words text-base font-black text-slate-950">{{ $item['lesson']->title }}</h3>
                        </a>
                        <a href="{{ route('flashcard.study', ['number' => $item['lesson']->number, 'mode' => 'srs']) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-lg border border-violet-200 bg-violet-50 px-4 py-2.5 text-sm font-bold text-violet-700 hover:bg-violet-100">
                            Ôn SRS bài này
                        </a>
                    </article>
                @empty
                    <div class="rounded-xl border border-slate-200 bg-white p-8 text-center text-slate-500 md:col-span-2 xl:col-span-3">
                        Chưa có bài học. Chạy: php artisan db:seed --class=MinnaSeeder
                    </div>
                @endforelse
            </div>
        </section>
    </main>

    <script>
        (function () {
            const form = document.getElementById('form-multi');
            const selectAll = document.getElementById('select-all');
            const selectedCount = document.getElementById('selected-count');

            function checkboxes() {
                return Array.from(document.querySelectorAll('#form-multi input[name="bai[]"]'));
            }

            function refreshSelectedCount() {
                const boxes = checkboxes();
                const checked = boxes.filter((box) => box.checked).length;
                if (selectedCount) {
                    selectedCount.textContent = checked + ' bài đã chọn';
                }
                if (selectAll) {
                    selectAll.textContent = checked === boxes.length && boxes.length > 0 ? 'Bỏ chọn' : 'Chọn tất cả';
                }
            }

            form?.addEventListener('submit', function (event) {
                const checked = checkboxes().filter((box) => box.checked);
                if (checked.length === 0) {
                    event.preventDefault();
                    alert('Vui lòng chọn ít nhất một bài học.');
                }
            });

            form?.addEventListener('change', refreshSelectedCount);

            selectAll?.addEventListener('click', function () {
                const boxes = checkboxes();
                const allChecked = boxes.every((box) => box.checked);
                boxes.forEach((box) => {
                    box.checked = !allChecked;
                });
                refreshSelectedCount();
            });

            refreshSelectedCount();
        })();
    </script>
    @include('layouts.footer')
</body>
</html>
