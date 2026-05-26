<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Từ vựng Minna - Chọn bài</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="container mx-auto px-4 max-w-5xl py-24">
        <a href="{{ route('minna.index') }}" class="text-red-600 hover:text-red-700 text-sm mb-6 inline-block">← Bài học Minna</a>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Từ vựng theo bài</h1>
        <p class="text-gray-600 mb-8">Chọn bài để xem bảng từ vựng. Bạn cũng có thể ôn bằng <a href="{{ route('flashcard.index') }}" class="text-red-600 hover:underline">Flashcard</a>.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($lessonsWithCount as $item)
                <a href="{{ route('vocabulary.show', $item['lesson']->number) }}"
                   class="block bg-white rounded-xl border p-6 hover:shadow-lg hover:border-red-200 transition">
                    <span class="text-xl font-bold text-red-600">Bài {{ str_pad($item['lesson']->number, 2, '0', STR_PAD_LEFT) }}</span>
                    <span class="ml-2 text-sm bg-red-100 text-red-800 px-2 py-0.5 rounded">{{ $item['count'] }} từ</span>
                    <h3 class="mt-2 text-lg font-semibold text-gray-900 japanese-text" style="font-family: 'Hiragino Sans','Yu Gothic',sans-serif">{{ $item['lesson']->title }}</h3>
                </a>
            @empty
                <div class="col-span-full text-center py-12 text-gray-500">Chưa có bài học. Chạy: php artisan db:seed --class=MinnaSeeder</div>
            @endforelse
        </div>
    </div>

    @include('layouts.footer')
</body>
</html>
