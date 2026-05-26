<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kanji {{ $level }} - Danh sách</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        .jp { font-family: 'Hiragino Sans','Yu Gothic','Meiryo',sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="container mx-auto px-4 max-w-5xl py-24">
        <a href="{{ route('kanji.index') }}" class="text-red-600 hover:text-red-700 text-sm mb-6 inline-block">← Chọn cấp khác</a>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Kanji {{ $level }}</h1>
            <a href="{{ route('kanji.flashcard', $level) }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                Ôn Flashcard
            </a>
        </div>

        @if($kanjis->isEmpty())
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center text-gray-500">
                Chưa có Kanji nào cho cấp {{ $level }}.
            </div>
        @else
            <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Chữ</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Nghĩa</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Âm On</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Âm Kun</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Nét</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($kanjis as $k)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <span class="jp text-2xl font-bold text-gray-900">{{ $k->character }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $k->meaning }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $k->on_reading ?: '–' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $k->kun_reading ?: '–' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500">{{ $k->stroke_count ?? '–' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
    @include('layouts.footer')
</body>
</html>
