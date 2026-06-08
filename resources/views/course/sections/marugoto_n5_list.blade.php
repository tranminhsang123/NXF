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
<body class="bg-slate-50 text-slate-900">
    @include('layouts.header')

    <main>
        <section class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-6xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <a href="{{ route('course.show', $level) }}" class="inline-flex items-center text-sm font-bold text-slate-600 hover:text-slate-950">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    {{ $level }}
                </a>
                <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-red-600">Danh sách bài</p>
                        <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-950">Marugoto N5</h1>
                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Học tiếng Nhật giao tiếp thực tế với giáo trình Marugoto N5.</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Số bài</p>
                        <p class="mt-1 text-2xl font-black text-slate-950">{{ $lessons->count() }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-6 sm:py-8">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                @foreach($lessons as $index => $lesson)
                    <a href="{{ route('course.marugoto.detail', ['level' => $level, 'id' => $lesson->id]) }}" 
                       class="group mb-3 block rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-red-200 hover:bg-red-50 hover:shadow-md sm:p-5">
                        <div class="flex items-center gap-4">
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg bg-red-600 text-base font-black text-white group-hover:bg-red-700">
                                {{ $index + 1 }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $lesson->bai }}</p>
                                <h2 class="mt-1 break-words text-base font-black text-slate-950 group-hover:text-red-700 sm:text-lg">{{ $lesson->title }}</h2>
                            </div>
                            <svg class="h-5 w-5 shrink-0 text-slate-400 transition group-hover:translate-x-1 group-hover:text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </a>
                @endforeach

                @if($lessons->isEmpty())
                    <div class="rounded-xl border border-slate-200 bg-white p-8 text-center">
                        <p class="text-slate-500">Chưa có bài Marugoto N5</p>
                    </div>
                @endif
            </div>
        </section>
    </main>

    @include('layouts.footer')
</body>
</html>
