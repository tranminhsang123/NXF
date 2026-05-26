<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $courseData['title'] }} - Marugoto N5</title>
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

    <!-- Hero Section -->
    <section class="pt-24 pb-12 {{ $courseData['bgColor'] }}">
        <div class="container mx-auto max-w-7xl px-4 md:px-6">
            <div class="mb-8">
                <a href="{{ route('course.show', $level) }}" class="inline-flex items-center text-gray-700 hover:text-gray-900 transition mb-6 group font-medium">
                    <svg class="w-5 h-5 mr-1 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Quay lại {{ $level }}</span>
                </a>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-3 text-center">
                    Marugoto N5
                </h1>
                <p class="text-center text-gray-600 text-base md:text-lg">Học tiếng Nhật giao tiếp thực tế với giáo trình Marugoto N5</p>
            </div>
        </div>
    </section>

    <!-- Lessons List -->
    <section class="py-12">
        <div class="container mx-auto max-w-5xl px-4 md:px-6 lg:px-8">
            <div class="space-y-3 md:space-y-4">
                @foreach($lessons as $index => $lesson)
                    <a href="{{ route('course.marugoto.detail', ['level' => $level, 'id' => $lesson->id]) }}" 
                       class="block bg-white rounded-lg p-5 md:p-6 shadow-sm hover:shadow-lg transition-all duration-200 border border-gray-200 hover:border-red-300 group">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-4">
                                    <span class="flex-shrink-0 w-10 h-10 md:w-12 md:h-12 bg-red-600 text-white rounded-lg flex items-center justify-center font-bold text-base md:text-lg group-hover:bg-red-700 transition">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg md:text-xl font-semibold text-gray-900 group-hover:text-red-600 transition mb-1 break-words">
                                            {{ $lesson->title }}
                                        </h3>
                                        <p class="text-sm md:text-base text-gray-600">{{ $lesson->bai }}</p>
                                    </div>
                                </div>
                            </div>
                            <svg class="w-6 h-6 text-gray-400 group-hover:text-red-600 transition flex-shrink-0 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($lessons->isEmpty())
                <div class="bg-gray-50 rounded-lg p-8 text-center">
                    <p class="text-gray-500">Chưa có bài Marugoto N5</p>
                </div>
            @endif
        </div>
    </section>

    @include('layouts.footer')
</body>
</html>

