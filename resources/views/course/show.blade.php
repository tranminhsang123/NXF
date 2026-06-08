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

        .course-section-card {
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        @media (min-width: 1024px) {
            .course-section-card:hover {
                transform: translateY(-3px);
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    @include('layouts.header')

    <main>
        <section class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <a href="{{ route('course.index') }}" class="inline-flex items-center text-sm font-bold text-slate-600 hover:text-slate-950">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Tất cả cấp độ
                </a>

                <div class="mt-5 grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-end">
                    <div>
                        <div class="inline-flex items-center gap-3 rounded-xl {{ $courseData['bgColor'] }} px-4 py-3">
                            <span class="text-3xl">{{ $courseData['icon'] }}</span>
                            <span class="text-sm font-black uppercase tracking-wide {{ $courseData['textColor'] }}">{{ $level }}</span>
                        </div>
                        <h1 class="mt-4 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">{{ $courseData['title'] }}</h1>
                        <p class="mt-2 text-base font-semibold text-slate-700">{{ $courseData['subtitle'] }}</p>
                        <p class="mt-4 max-w-3xl text-sm leading-6 text-slate-600 sm:text-base">{{ $courseData['description'] }}</p>
                    </div>

                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Trạng thái nội dung</p>
                        @if(!empty($courseData['sections']))
                            <p class="mt-2 text-2xl font-black text-slate-950">{{ count($courseData['sections']) }} phần học</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Chọn một phần bên dưới để vào danh sách bài học.</p>
                        @else
                            <p class="mt-2 text-2xl font-black text-slate-950">Đang chuẩn bị</p>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Cấp độ này đã có tổng quan, nội dung chi tiết sẽ được mở sau.</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="py-6 sm:py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-sm font-bold text-red-600">Nội dung khóa học</p>
                        <h2 class="mt-1 text-2xl font-black text-slate-950">Chọn phần để học</h2>
                    </div>
                    <a href="{{ route('course.index') }}" class="text-sm font-bold text-slate-600 hover:text-slate-950">Đổi cấp độ</a>
                </div>

                @if(!empty($courseData['sections']))
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($courseData['sections'] as $index => $section)
                            @if(isset($section['disabled']) && $section['disabled'])
                                <div class="course-section-card rounded-xl border border-slate-200 bg-white p-5 opacity-80">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-2xl">{{ $section['icon'] }}</div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Sắp có</p>
                                            <h3 class="mt-1 text-lg font-black text-slate-950">{{ $section['title'] }}</h3>
                                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $section['description'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('course.section', ['level' => $level, 'sectionType' => $section['type']]) }}" class="course-section-card group rounded-xl border {{ $courseData['borderColor'] }} bg-white p-5 shadow-sm hover:shadow-md">
                                    <div class="flex items-start gap-4">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $courseData['bgColor'] }} text-2xl">{{ $section['icon'] }}</div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-xs font-bold uppercase tracking-wide {{ $courseData['textColor'] }}">Phần {{ $index + 1 }}</p>
                                            <h3 class="mt-1 text-lg font-black text-slate-950 group-hover:text-red-700">{{ $section['title'] }}</h3>
                                            <p class="mt-2 text-sm leading-6 text-slate-600">{{ $section['description'] }}</p>
                                            <span class="mt-4 inline-flex items-center text-sm font-bold {{ $courseData['textColor'] }}">
                                                Học ngay
                                                <svg class="ml-1 h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-slate-200 bg-white p-6 text-center shadow-sm">
                        <p class="text-lg font-black text-slate-950">Nội dung đang được chuẩn bị</p>
                        <p class="mx-auto mt-2 max-w-xl text-sm leading-6 text-slate-600">Bạn có thể bắt đầu với N5 trước, hoặc quay lại sau khi cấp độ này có bài học chi tiết.</p>
                        <a href="{{ route('course.show', 'n5') }}" class="mt-5 inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">Vào N5</a>
                    </div>
                @endif
            </div>
        </section>
    </main>

    @include('layouts.footer')
</body>
</html>
