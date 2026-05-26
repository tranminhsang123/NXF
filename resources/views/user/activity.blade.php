<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử học tập - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="pt-24 pb-12 min-h-screen">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Lịch sử học tập</h1>
                    <p class="text-gray-600 mt-1">Tổng hợp bài đã học, phần đã xong, quiz và flashcard.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('user.progress') }}" class="text-sm font-semibold text-red-600 hover:text-red-700">Tien do</a>
                    <a href="{{ route('user.dashboard') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Dashboard</a>
                </div>
            </div>

            @if(empty($groups))
                <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-500">
                    Chưa có hoạt động học tập nào.
                </div>
            @else
                <div class="space-y-6">
                    @foreach($groups as $date => $items)
                        <section class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                                <h2 class="font-bold text-gray-900">{{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}</h2>
                            </div>
                            <div class="divide-y divide-gray-100">
                                @foreach($items as $item)
                                    @php
                                        $tone = match($item['type']) {
                                            'lesson_completed', 'section_completed', 'quiz_passed' => 'bg-green-100 text-green-700',
                                            'quiz_failed' => 'bg-amber-100 text-amber-700',
                                            'flashcard_reviewed' => 'bg-violet-100 text-violet-700',
                                            default => 'bg-red-100 text-red-700',
                                        };
                                    @endphp
                                    <a href="{{ $item['url'] ?? '#' }}" class="block px-4 py-4 hover:bg-gray-50">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex items-start gap-3">
                                                <span class="mt-0.5 inline-flex w-8 h-8 rounded-full items-center justify-center text-xs font-bold {{ $tone }}">
                                                    {{ strtoupper(substr($item['type'], 0, 1)) }}
                                                </span>
                                                <div>
                                                    <p class="font-semibold text-gray-900">{{ $item['title'] }}</p>
                                                    <p class="text-sm text-gray-500 mt-1">{{ $item['subtitle'] }}</p>
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500 whitespace-nowrap">{{ $item['at']->format('H:i') }}</span>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @include('layouts.footer')
</body>
</html>
