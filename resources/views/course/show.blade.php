<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $courseData['title'] }} - Học Tiếng Nhật</title>
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
            <div class="text-center mb-8">
                <div class="inline-block mb-4">
                    <span class="text-6xl">{{ $courseData['icon'] }}</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-3">
                    {{ $courseData['title'] }}
                </h1>
                <p class="text-lg md:text-xl text-gray-600 mb-6">
                    {{ $courseData['subtitle'] }}
                </p>
            </div>
        </div>
    </section>

    <!-- Course Description -->
    <section class="py-12 bg-white">
        <div class="container mx-auto max-w-7xl px-4 md:px-6">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-6">Giới thiệu khóa học</h2>
                <p class="text-lg text-gray-700 leading-relaxed">
                    {{ $courseData['description'] }}
                </p>
            </div>
        </div>
    </section>

    <!-- Course Sections -->
    <section class="py-12 bg-gradient-to-br from-gray-50 to-gray-100">
        <div class="container mx-auto max-w-7xl px-4 md:px-6">
            <div class="max-w-6xl mx-auto">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-8 text-center">Nội dung khóa học</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach(($courseData['sections'] ?? []) as $index => $section)
                    <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition {{ $courseData['borderColor'] }} border-2 group cursor-pointer">
                        <div class="flex flex-col items-center text-center">
                            <div class="text-5xl mb-4 group-hover:scale-110 transition-transform">
                                {{ $section['icon'] }}
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-2">{{ $section['title'] }}</h3>
                            <p class="text-gray-600 text-sm mb-4">{{ $section['description'] }}</p>
                            @if(isset($section['disabled']) && $section['disabled'])
                                <button class="w-full bg-gray-300 text-gray-500 py-2 rounded-lg font-semibold cursor-not-allowed" disabled>
                                    Sắp có
                                </button>
                            @else
                                <a href="{{ route('course.section', ['level' => $level, 'sectionType' => $section['type']]) }}" class="block w-full {{ $courseData['buttonColor'] }} text-white py-2 rounded-lg font-semibold transition text-center">
                                    Học ngay
                                </a>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- Back Button -->
    <section class="py-8 bg-white">
        <div class="container mx-auto max-w-7xl px-4 md:px-6">
            <div class="text-center">
                <a href="{{ route('home') }}" class="inline-block bg-gray-200 text-gray-700 px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition">
                    ← Quay lại trang chủ
                </a>
            </div>
        </div>
    </section>

    @include('layouts.footer')
</body>
</html>

