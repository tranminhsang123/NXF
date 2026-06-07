@php
    $categories = [
        ['key' => 'vocabulary', 'label' => 'Từ vựng', 'desc' => 'Từ trong bài Minna', 'emoji' => '📖'],
        ['key' => 'kanji', 'label' => 'Kanji', 'desc' => 'Hán tự theo cấp JLPT', 'emoji' => '漢'],
        ['key' => 'lessons', 'label' => 'Bài Minna', 'desc' => 'Tiêu đề & mô tả bài', 'emoji' => '📚'],
        ['key' => 'sentence_patterns', 'label' => 'Mẫu câu', 'desc' => 'Cấu trúc câu mẫu', 'emoji' => '💬'],
        ['key' => 'grammar', 'label' => 'Ngữ pháp', 'desc' => 'Điểm ngữ pháp bài học', 'emoji' => '📝'],
        ['key' => 'favorites', 'label' => 'Từ yêu thích', 'desc' => 'Flashcard đã lưu', 'emoji' => '⭐'],
        ['key' => 'related', 'label' => 'Gợi ý liên quan', 'desc' => 'Kết quả gần với từ khóa', 'emoji' => '✨'],
    ];
@endphp
<div class="grid gap-3 sm:grid-cols-2">
    @foreach($categories as $cat)
        <a href="{{ route('search.index', ['type' => $cat['key']]) }}"
           class="flex items-start gap-3 rounded-xl border border-gray-200 bg-white p-4 shadow-sm transition hover:border-red-300 hover:shadow-md">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-lg">{{ $cat['emoji'] }}</span>
            <div>
                <p class="font-semibold text-gray-900">{{ $cat['label'] }}</p>
                <p class="text-sm text-gray-500">{{ $cat['desc'] }}</p>
            </div>
        </a>
    @endforeach
</div>
