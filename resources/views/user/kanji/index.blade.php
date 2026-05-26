<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ôn Kanji theo cấp độ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        .jp { font-family: 'Hiragino Sans','Yu Gothic','Meiryo',sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="container mx-auto px-4 max-w-4xl py-24">
        <a href="{{ route('alphabet.index') }}" class="text-red-600 hover:text-red-700 text-sm mb-6 inline-block">← Bảng chữ cái</a>
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Ôn Kanji theo cấp độ</h1>
        <p class="text-gray-600 mb-8">Chọn cấp độ JLPT để xem danh sách Kanji hoặc ôn bằng flashcard.</p>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach(\App\Services\KanjiService::LEVELS as $level)
                @php $count = $countsByLevel[$level] ?? 0; @endphp
                <div class="bg-white rounded-xl border border-gray-200 p-6 flex flex-col">
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $level }}</h2>
                    <p class="text-gray-600 text-sm mb-4">{{ $count }} chữ</p>
                    <div class="mt-auto flex gap-2">
                        <a href="{{ route('kanji.list', $level) }}" class="flex-1 py-2 px-4 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-center text-sm font-medium">
                            Danh sách
                        </a>
                        <a href="{{ route('kanji.flashcard', $level) }}" class="flex-1 py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 text-center text-sm font-medium">
                            Flashcard
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        @if($countsByLevel->isEmpty())
            <div class="mt-8 text-center py-12 bg-white rounded-xl border border-gray-200 text-gray-500">
                Chưa có dữ liệu Kanji. Admin có thể thêm qua khu vực quản trị.
            </div>
        @endif
    </div>
    @include('layouts.footer')
</body>
</html>
