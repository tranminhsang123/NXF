<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lộ trình Minna no Nihongo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="container mx-auto px-4 max-w-5xl py-24">
        <div class="mb-8 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Lộ trình Minna no Nihongo</h1>
                <p class="text-gray-600 mt-2">Theo dõi 50 bài theo thứ tự, phần nào xong sẽ cập nhật vào tiến độ.</p>
            </div>
            <a href="{{ route('minna.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">
                Danh sách bài
            </a>
        </div>

        <div class="relative">
            <div class="absolute left-5 top-4 bottom-4 w-px bg-gray-200 hidden md:block"></div>
            <div class="space-y-4">
                @foreach($lessons as $lesson)
                    @php
                        $progress = ($progressByLessonId ?? collect())->get($lesson->id);
                        $sectionProgress = ($sectionProgressByLessonId ?? collect())->get($lesson->id, ['total' => 5, 'completed' => 0, 'percent' => 0]);
                        $isCompleted = $progress && $progress->status === \App\Models\UserProgress::STATUS_COMPLETED;
                        $isInProgress = $progress && $progress->status === \App\Models\UserProgress::STATUS_IN_PROGRESS;
                    @endphp
                    <a href="{{ route('minna.show', ['number' => $lesson->number]) }}"
                       class="relative block bg-white border {{ $isCompleted ? 'border-green-200' : ($isInProgress ? 'border-amber-200' : 'border-gray-200') }} rounded-xl p-4 md:ml-12 hover:shadow-md transition">
                        <span class="hidden md:flex absolute -left-12 top-5 w-10 h-10 rounded-full items-center justify-center text-sm font-bold {{ $isCompleted ? 'bg-green-600 text-white' : ($isInProgress ? 'bg-amber-500 text-white' : 'bg-gray-200 text-gray-700') }}">
                            {{ str_pad($lesson->number, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="md:hidden text-sm font-bold text-red-600">Bài {{ str_pad($lesson->number, 2, '0', STR_PAD_LEFT) }}</span>
                                    @if($isCompleted)
                                        <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-full">Đã xong</span>
                                    @elseif($isInProgress)
                                        <span class="text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-1 rounded-full">Đang học</span>
                                    @else
                                        <span class="text-xs font-semibold text-gray-600 bg-gray-100 px-2 py-1 rounded-full">Chưa học</span>
                                    @endif
                                </div>
                                <h2 class="font-bold text-gray-900">Bài {{ $lesson->number }} - {{ $lesson->title }}</h2>
                                <p class="text-sm text-gray-500 mt-1">{{ $sectionProgress['completed'] ?? 0 }}/{{ $sectionProgress['total'] ?? 0 }} phần đã hoàn thành</p>
                            </div>
                            <div class="md:w-56">
                                <div class="flex justify-between text-xs text-gray-500 mb-1">
                                    <span>Tiến độ</span>
                                    <span>{{ $sectionProgress['percent'] ?? 0 }}%</span>
                                </div>
                                <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                                    <div class="h-2 rounded-full {{ $isCompleted ? 'bg-green-600' : 'bg-red-600' }}" style="width: {{ $sectionProgress['percent'] ?? 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    @include('layouts.footer')
</body>
</html>
