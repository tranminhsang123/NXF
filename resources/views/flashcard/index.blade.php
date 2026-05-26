<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flashcard - Ôn từ vựng Minna</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="container mx-auto px-4 max-w-5xl py-24">
        <a href="{{ route('minna.index') }}" class="text-red-600 hover:text-red-700 text-sm mb-6 inline-block">← Bài học Minna</a>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Flashcard</h1>
        <p class="text-gray-600 mb-4">Chọn bài để ôn từ vựng. Có thể chọn nhiều bài để ôn cùng lúc. Nhấn thẻ để lật xem nghĩa.</p>

        @if(session('warning'))
            <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-sm">{{ session('warning') }}</div>
        @endif

        {{-- Ôn nhiều bài --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-4">
                <p class="text-xs text-gray-500">Thẻ đã ôn</p>
                <p class="text-2xl font-bold text-gray-900">{{ $srsDashboard['reviewed_count'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl border border-red-100 p-4">
                <p class="text-xs text-gray-500">Đến hạn</p>
                <p class="text-2xl font-bold text-red-600">{{ $srsDashboard['due_count'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl border border-amber-100 p-4">
                <p class="text-xs text-gray-500">Thẻ yếu</p>
                <p class="text-2xl font-bold text-amber-600">{{ $srsDashboard['weak_count'] ?? 0 }}</p>
            </div>
            <div class="bg-white rounded-xl border border-violet-100 p-4">
                <p class="text-xs text-gray-500">Đến hạn 24h tới</p>
                <p class="text-2xl font-bold text-violet-600">{{ $srsDashboard['upcoming_count'] ?? 0 }}</p>
            </div>
        </div>

        @if(!empty($srsDashboard['weak_lessons']) && $srsDashboard['weak_lessons']->isNotEmpty())
            <div class="mb-8 bg-amber-50 border border-amber-200 rounded-xl p-4">
                <p class="text-sm font-semibold text-amber-900 mb-2">Bài có thẻ yếu cần ôn lại</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($srsDashboard['weak_lessons'] as $weakLesson)
                        <a href="{{ route('flashcard.study', ['number' => $weakLesson->number, 'mode' => 'srs']) }}" class="px-3 py-1.5 rounded-lg bg-white border border-amber-200 text-sm font-semibold text-amber-800 hover:bg-amber-100">
                            Bài {{ $weakLesson->number }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @auth
            <div class="mb-8 rounded-xl border border-emerald-200 bg-emerald-50 p-4">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-emerald-900">Bộ từ yêu thích</p>
                        <p class="text-sm text-emerald-700 mt-1">Bạn đã lưu {{ $favoriteCount ?? 0 }} từ/câu để ôn riêng.</p>
                    </div>
                    <a href="{{ route('flashcard.favorites') }}" class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                        Ôn từ yêu thích
                    </a>
                </div>
            </div>
        @endauth

        @if(!empty($srsDashboard['weak_cards']) && $srsDashboard['weak_cards']->isNotEmpty())
            <div class="mb-8 bg-white border border-gray-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="font-semibold text-gray-900">Thẻ yếu cụ thể</h2>
                    <span class="text-xs text-gray-500">Sắp xếp theo độ khó nhớ</span>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($srsDashboard['weak_cards'] as $weakCard)
                        <div class="rounded-lg border border-amber-100 bg-amber-50 p-3">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="jp font-bold text-gray-900" style="font-family: 'Hiragino Sans','Yu Gothic',sans-serif">{{ $weakCard['front'] }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ $weakCard['back'] }}</p>
                                </div>
                                @if($weakCard['lesson_number'])
                                    <span class="text-xs font-semibold text-amber-800 bg-white px-2 py-1 rounded-full">Bài {{ $weakCard['lesson_number'] }}</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-2">Mức nhớ {{ $weakCard['last_quality'] ?? '-' }} - EF {{ $weakCard['ease_factor'] ?? '-' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="GET" action="{{ route('flashcard.study.multi') }}" id="form-multi" class="mb-8 p-4 bg-white rounded-xl border border-gray-200">
            <h2 class="font-semibold text-gray-900 mb-3">Ôn nhiều bài cùng lúc</h2>
            <div class="flex flex-wrap gap-3 mb-4">
                @foreach($lessonsWithCount as $item)
                    <label class="inline-flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100">
                        <input type="checkbox" name="bai[]" value="{{ $item['lesson']->number }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="font-medium">Bài {{ str_pad($item['lesson']->number, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="text-xs text-gray-500">({{ $item['count'] }} từ)</span>
                    </label>
                @endforeach
            </div>
            <div class="flex flex-wrap gap-2">
                <input type="hidden" name="shuffle" value="1">
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">
                    Ôn các bài đã chọn (đầy đủ thẻ)
                </button>
                <button type="submit" name="mode" value="srs" class="px-4 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 font-medium">
                    Ôn tập (đến hạn + thẻ mới)
                </button>
                <button type="button" id="select-all" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 font-medium">
                    Chọn tất cả
                </button>
            </div>
        </form>

        {{-- Chọn 1 bài --}}
        <h2 class="font-semibold text-gray-900 mb-4">Hoặc chọn một bài</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($lessonsWithCount as $item)
                <div class="block bg-white rounded-xl border p-6 hover:shadow-lg hover:border-red-200 transition">
                    <a href="{{ route('flashcard.study', $item['lesson']->number) }}" class="block">
                        <span class="text-xl font-bold text-red-600">Bài {{ str_pad($item['lesson']->number, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="ml-2 text-sm bg-red-100 text-red-800 px-2 py-0.5 rounded">{{ $item['count'] }} từ</span>
                        <h3 class="mt-2 text-lg font-semibold text-gray-900 jp" style="font-family: 'Hiragino Sans','Yu Gothic',sans-serif">{{ $item['lesson']->title }}</h3>
                    </a>
                    <a href="{{ route('flashcard.study', ['number' => $item['lesson']->number, 'mode' => 'srs']) }}"
                       class="mt-3 inline-block text-sm text-violet-600 hover:text-violet-800 font-medium">
                        → Ôn tập
                    </a>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">Chưa có bài học. Chạy: php artisan db:seed --class=MinnaSeeder</div>
            @endforelse
        </div>
    </div>

        <script>
        document.getElementById('form-multi')?.addEventListener('submit', function(e) {
            const cbs = document.querySelectorAll('#form-multi input[name="bai[]"]:checked');
            if (cbs.length === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một bài học.');
            }
        });
        document.getElementById('select-all')?.addEventListener('click', function() {
            const cbs = document.querySelectorAll('#form-multi input[name="bai[]"]');
            const allChecked = Array.from(cbs).every(c => c.checked);
            cbs.forEach(c => c.checked = !allChecked);
            this.textContent = allChecked ? 'Chọn tất cả' : 'Bỏ chọn';
        });
    </script>
    @include('layouts.footer')
</body>
</html>
