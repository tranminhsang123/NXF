<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Minna no Nihongo - Danh sách bài học</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="container mx-auto px-6 py-24">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Minna no Nihongo</h1>
            <p class="text-gray-600">Chọn bài học để bắt đầu học tiếng Nhật</p>
        </div>

        <div class="mb-8 flex justify-end">
            <a href="{{ route('minna.roadmap') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-semibold hover:bg-gray-800">
                Lộ trình học
            </a>
        </div>

        <form method="GET" action="{{ route('minna.index') }}" class="bg-white border border-gray-200 rounded-xl p-4 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-[1fr_220px_auto] gap-3">
                <input type="search" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Tìm theo số bài, tiêu đề, mô tả..." class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                <select name="status" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                    <option value="all" @selected(($filters['status'] ?? 'all') === 'all')>Tất cả trạng thái</option>
                    <option value="not_started" @selected(($filters['status'] ?? 'all') === 'not_started')>Chưa học</option>
                    <option value="in_progress" @selected(($filters['status'] ?? 'all') === 'in_progress')>Đang học</option>
                    <option value="completed" @selected(($filters['status'] ?? 'all') === 'completed')>Đã xong</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700">Lọc</button>
                    <a href="{{ route('minna.index') }}" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 font-semibold hover:bg-gray-300">Đặt lại</a>
                </div>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($lessons as $lesson)
                @php
                    $lessonProgress = ($progressByLessonId ?? collect())->get($lesson->id);
                    $sectionProgress = ($sectionProgressByLessonId ?? collect())->get($lesson->id, ['total' => 5, 'completed' => 0, 'percent' => 0]);
                    $isCompleted = $lessonProgress && $lessonProgress->status === \App\Models\UserProgress::STATUS_COMPLETED;
                    $isInProgress = $lessonProgress && $lessonProgress->status === \App\Models\UserProgress::STATUS_IN_PROGRESS;
                @endphp
                <a href="{{ route('minna.show', $lesson->number) }}" 
                   class="block bg-white rounded-lg shadow-md hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border {{ $isCompleted ? 'border-green-300' : ($isInProgress ? 'border-amber-300' : 'border-gray-200') }}">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-2xl font-bold text-red-600">Bài {{ str_pad($lesson->number, 2, '0', STR_PAD_LEFT) }}</span>
                            @if($isCompleted)
                                <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-full">Đã xong</span>
                            @elseif($isInProgress)
                                <span class="text-xs font-semibold text-amber-700 bg-amber-100 px-2 py-1 rounded-full">Đang học</span>
                            @else
                                <span class="text-sm text-gray-500">#{{ $lesson->number }}</span>
                            @endif
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                            {{ $lesson->title }}
                        </h3>
                        @if($lesson->description)
                            <p class="text-sm text-gray-600 line-clamp-2">{{ $lesson->description }}</p>
                        @endif
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-between gap-3 text-sm text-gray-500">
                                <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span>5 phần</span>
                                </div>
                                @if($lessonProgress && $lessonProgress->last_accessed_at)
                                    <span class="text-xs text-gray-400">{{ $lessonProgress->last_accessed_at->format('d/m') }}</span>
                                @endif
                            </div>
                            @auth
                                <div class="mt-3">
                                    <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                        <span>{{ $sectionProgress['completed'] ?? 0 }}/{{ $sectionProgress['total'] ?? 0 }} phần</span>
                                        <span>{{ $sectionProgress['percent'] ?? 0 }}%</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                                        <div class="h-2 rounded-full {{ $isCompleted ? 'bg-green-600' : 'bg-red-600' }}" style="width: {{ $sectionProgress['percent'] ?? 0 }}%"></div>
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        @if($lessons->isEmpty())
            <div class="text-center py-12">
                <p class="text-gray-500 text-lg">Chưa có bài học nào. Vui lòng chạy seeder để thêm dữ liệu.</p>
                <p class="text-gray-400 text-sm mt-2">php artisan db:seed --class=MinnaSeeder</p>
            </div>
        @endif
    </div>

    @include('layouts.footer')
</body>
</html>
