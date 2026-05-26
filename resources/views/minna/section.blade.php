<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $section->title }} - {{ $lesson->title }}</title>
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

    <div class="container mx-auto px-6 py-24">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <a href="{{ route('minna.index') }}" class="text-red-600 hover:text-red-700">Danh sách bài học</a>
            <span class="mx-2 text-gray-400">/</span>
            <a href="{{ route('minna.show', $lesson->number) }}" class="text-red-600 hover:text-red-700">{{ $lesson->title }}</a>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-gray-600">{{ $section->title }}</span>
        </nav>

        <!-- Lesson Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $lesson->title }}</h1>
            <span class="text-lg text-red-600">Bài {{ str_pad($lesson->number, 2, '0', STR_PAD_LEFT) }}</span>
        </div>

        <!-- Section Content -->
        <div class="bg-white rounded-lg shadow-md p-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-6 pb-4 border-b-2 border-red-600">
                {{ $section->title }}
            </h2>

            @if($section->content)
                @if($section->key === 'tu-vung')
                    @include('minna.sections.tu-vung', ['content' => $section->content])
                @elseif($section->key === 'ngu-phap')
                    @include('minna.sections.ngu-phap', ['content' => $section->content])
                @elseif($section->key === 'luyen-doc')
                    @include('minna.sections.luyen-doc', ['content' => $section->content])
                @elseif($section->key === 'hoi-thoai')
                    @include('minna.sections.hoi-thoai', ['content' => $section->content])
                @elseif($section->key === 'han-tu')
                    @include('minna.sections.han-tu', ['content' => $section->content])
                @endif
            @else
                <p class="text-gray-500 italic">Nội dung đang được cập nhật...</p>
            @endif
        </div>
    </div>

    @include('layouts.footer')
</body>
</html>

