<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tổng hợp N - Chinh phục JLPT từ N5 đến N1</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .course-card {
            transition: border-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        @media (min-width: 1024px) {
            .course-card:hover {
                transform: translateY(-3px);
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    @include('layouts.header')

    @php
        $levels = [
            [
                'slug' => 'n5',
                'code' => 'N5',
                'title' => 'Sơ cấp',
                'subtitle' => 'Bắt đầu với nền tảng JLPT: từ vựng, đọc hiểu và giáo trình N5.',
                'icon' => '🌱',
                'status' => 'Đang mở',
                'meta' => 'Có nội dung học',
                'classes' => 'border-red-200 bg-red-50 text-red-700',
                'button' => 'bg-red-600 text-white hover:bg-red-700',
            ],
            [
                'slug' => 'n4',
                'code' => 'N4',
                'title' => 'Sơ trung cấp',
                'subtitle' => 'Nâng cấp từ N5 với mẫu câu và bài đọc dài hơn.',
                'icon' => '🌿',
                'status' => 'Đang chuẩn bị',
                'meta' => 'Tổng quan',
                'classes' => 'border-orange-200 bg-orange-50 text-orange-700',
                'button' => 'border border-orange-200 bg-white text-orange-700 hover:bg-orange-50',
            ],
            [
                'slug' => 'n3',
                'code' => 'N3',
                'title' => 'Trung cấp',
                'subtitle' => 'Củng cố đọc hiểu, ngữ pháp và giao tiếp thực tế.',
                'icon' => '🌳',
                'status' => 'Đang chuẩn bị',
                'meta' => 'Tổng quan',
                'classes' => 'border-yellow-200 bg-yellow-50 text-yellow-700',
                'button' => 'border border-yellow-200 bg-white text-yellow-700 hover:bg-yellow-50',
            ],
            [
                'slug' => 'n2',
                'code' => 'N2',
                'title' => 'Trung cao cấp',
                'subtitle' => 'Hướng tới đọc hiểu sâu và sử dụng tiếng Nhật trong công việc.',
                'icon' => '🏔️',
                'status' => 'Đang chuẩn bị',
                'meta' => 'Tổng quan',
                'classes' => 'border-blue-200 bg-blue-50 text-blue-700',
                'button' => 'border border-blue-200 bg-white text-blue-700 hover:bg-blue-50',
            ],
            [
                'slug' => 'n1',
                'code' => 'N1',
                'title' => 'Cao cấp',
                'subtitle' => 'Mục tiêu thành thạo: đọc nhanh, hiểu sâu, dùng tự nhiên.',
                'icon' => '🏆',
                'status' => 'Đang chuẩn bị',
                'meta' => 'Tổng quan',
                'classes' => 'border-purple-200 bg-purple-50 text-purple-700',
                'button' => 'border border-purple-200 bg-white text-purple-700 hover:bg-purple-50',
            ],
        ];
    @endphp

    <main>
        <section class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 sm:py-10 lg:px-8">
                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-end">
                    <div>
                        <p class="text-sm font-bold text-red-600">Tổng hợp JLPT</p>
                        <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">Chọn cấp độ học phù hợp</h1>
                        <p class="mt-3 max-w-2xl text-sm leading-6 text-slate-600 sm:text-base">
                            Bắt đầu từ N5 hoặc xem trước lộ trình các cấp cao hơn. Nội dung đang ưu tiên phần N5 để học ngay.
                        </p>
                    </div>
                    <div class="grid grid-cols-3 gap-2 rounded-xl border border-slate-200 bg-slate-50 p-3 text-center">
                        <div>
                            <p class="text-2xl font-black text-slate-950">5</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">Cấp độ</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-950">N5</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">Đang mở</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-slate-950">24K+</p>
                            <p class="mt-1 text-xs font-semibold text-slate-500">Từ vựng</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-6 sm:py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                    @foreach($levels as $item)
                        <a href="{{ route('course.show', $item['slug']) }}" class="course-card group flex min-h-[13rem] flex-col rounded-xl border bg-white p-4 shadow-sm hover:border-red-200 hover:shadow-md">
                            <div class="flex items-start justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $item['classes'] }}">
                                        <span class="text-2xl">{{ $item['icon'] }}</span>
                                    </div>
                                    <div>
                                        <p class="text-2xl font-black text-slate-950">{{ $item['code'] }}</p>
                                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $item['meta'] }}</p>
                                    </div>
                                </div>
                                <span class="rounded-full {{ $item['classes'] }} px-2.5 py-1 text-xs font-bold">{{ $item['status'] }}</span>
                            </div>
                            <div class="mt-4 flex-1">
                                <h2 class="text-lg font-black text-slate-950">{{ $item['title'] }}</h2>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $item['subtitle'] }}</p>
                            </div>
                            <div class="mt-4 inline-flex min-h-10 items-center justify-center rounded-lg px-3 py-2 text-sm font-bold {{ $item['button'] }}">
                                {{ $item['code'] === 'N5' ? 'Vào nội dung' : 'Xem tổng quan' }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-white py-8">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-4 md:grid-cols-3">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-bold text-slate-950">Lộ trình rõ ràng</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Mỗi cấp độ tách theo mục tiêu JLPT để bạn biết nên học gì tiếp theo.</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-bold text-slate-950">Ưu tiên nội dung học thật</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">N5 đang có các phần học cụ thể như Speed Master, Marugoto và luyện đọc.</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-sm font-bold text-slate-950">Dễ quay lại</p>
                        <p class="mt-2 text-sm leading-6 text-slate-600">Các trang bài học được làm theo dạng danh sách để tiếp tục học nhanh hơn trên mobile.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    @include('layouts.footer')
</body>
</html>
