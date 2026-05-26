<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiến độ học tập - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="pt-24 pb-12 min-h-screen">
        <div class="container mx-auto px-4 max-w-5xl">
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-1">
                        Tiến độ học tập của bạn
                    </h1>
                    <p class="text-gray-600 text-sm md:text-base">
                        Xem lại các bài Minna no Nihongo bạn đã học và hoàn thành.
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('user.activity') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900">
                        Lịch sử học
                    </a>
                    <a href="{{ route('user.statistics') }}" class="inline-flex items-center text-sm text-red-600 hover:text-red-700">
                        Thống kê chi tiết
                    </a>
                    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700">
                        &larr; Dashboard
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-gray-800 uppercase tracking-wide">
                        Minna no Nihongo
                    </h2>
                    <span class="text-xs text-gray-500">
                        Tổng cộng: {{ $minnaProgresses->count() }} bài
                    </span>
                </div>

                @if($minnaProgresses->isEmpty())
                    <div class="p-6 text-center text-gray-500 text-sm">
                        Bạn chưa bắt đầu bài Minna nào. Hãy vào mục
                        <a href="{{ route('minna.index') }}" class="text-blue-600 hover:text-blue-700 font-medium">
                            Minna no Nihongo
                        </a>
                        để bắt đầu học nhé!
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($minnaProgresses as $progress)
                            @php
                                $lesson = $progress->lesson;
                                $sectionProgress = ($sectionProgressByLessonId ?? collect())->get($progress->lesson_id, ['total' => 0, 'completed' => 0, 'percent' => 0]);
                            @endphp
                            <a
                                href="{{ route('minna.show', ['number' => $lesson?->number]) }}"
                                class="block px-4 py-4 hover:bg-gray-50 transition"
                            >
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-sm font-semibold text-gray-900">
                                                @if($lesson)
                                                    Bài {{ $lesson->number }}: {{ $lesson->title }}
                                                @else
                                                    Bài #{{ $progress->lesson_id }}
                                                @endif
                                            </p>
                                            @if($progress->status === \App\Models\UserProgress::STATUS_COMPLETED)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                    Đã hoàn thành
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                                                    Đang học
                                                </span>
                                            @endif
                                        </div>
                                        @if($lesson && $lesson->description)
                                            <p class="text-xs text-gray-600 line-clamp-2">
                                                {{ $lesson->description }}
                                            </p>
                                        @endif
                                        <div class="mt-3 max-w-sm">
                                            <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                                <span>{{ $sectionProgress['completed'] ?? 0 }}/{{ $sectionProgress['total'] ?? 0 }} phần</span>
                                                <span>{{ $sectionProgress['percent'] ?? 0 }}%</span>
                                            </div>
                                            <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                                                <div class="h-2 rounded-full {{ $progress->status === \App\Models\UserProgress::STATUS_COMPLETED ? 'bg-green-600' : 'bg-red-600' }}" style="width: {{ $sectionProgress['percent'] ?? 0 }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($progress->completed_at)
                                            <p class="text-xs text-gray-500">
                                                Hoàn thành:
                                                <span class="font-medium">
                                                    {{ $progress->completed_at->format('d/m/Y H:i') }}
                                                </span>
                                            </p>
                                        @endif
                                        @if($progress->last_accessed_at)
                                            <p class="text-xs text-gray-500">
                                                Lần học gần nhất:
                                                <span class="font-medium">
                                                    {{ $progress->last_accessed_at->format('d/m/Y H:i') }}
                                                </span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @include('layouts.footer')
</body>
</html>
