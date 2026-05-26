<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php
        $sectionTitle = match($sectionType) {
            'speed_master_n5' => 'Speed Master N5',
            'luyen_doc' => 'Luyện đọc',
            'marugoto_n5' => 'Marugoto N5',
            default => 'Nội dung khóa học'
        };
    @endphp
    <title>{{ $courseData['title'] }} - {{ $sectionTitle }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .japanese-text {
            font-family: 'Hiragino Sans', 'Yu Gothic', 'Meiryo', sans-serif;
            font-size: 1.2em;
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
                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-3 text-center">
                    {{ $sectionTitle }}
                </h1>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-12">
        <div class="container mx-auto max-w-7xl px-4 md:px-6">
            <!-- This section is only for speed_master_n5 which now redirects to list view -->
        </div>
    </section>

    @include('layouts.footer')
</body>
</html>

