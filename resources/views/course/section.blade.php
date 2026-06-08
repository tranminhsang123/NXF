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
            line-height: 1.75;
            overflow-wrap: break-word;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    @include('layouts.header')

    <main>
        <section class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <a href="{{ route('course.show', $level) }}" class="inline-flex items-center text-sm font-bold text-slate-600 hover:text-slate-950">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    {{ $level }}
                </a>
                <p class="mt-5 text-sm font-bold text-red-600">Nội dung khóa học</p>
                <h1 class="mt-1 break-words text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">{{ $sectionTitle }}</h1>
            </div>
        </section>

        <section class="py-6 sm:py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                    <p class="text-lg font-black text-slate-950">Phần học này đang được sắp xếp lại</p>
                    <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-600">Các phần N5 hiện đã được đưa về dạng danh sách bài học để dễ học hơn trên mobile.</p>
                    <a href="{{ route('course.show', $level) }}" class="mt-5 inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">Về {{ $level }}</a>
                </div>
            </div>
        </section>
    </main>

    @include('layouts.footer')
</body>
</html>
