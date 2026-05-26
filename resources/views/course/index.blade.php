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
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-hover:hover {
            transform: translateY(-4px);
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    @include('layouts.header')

    <!-- Hero Section -->
    <section class="relative pt-28 pb-20 overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-red-50/50 via-blue-50/30 to-purple-50/50"></div>
        <div class="relative container mx-auto max-w-6xl px-4 md:px-6">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl mb-6 shadow-lg">
                    <span class="text-4xl">🎯</span>
                </div>
                <h1 class="text-5xl md:text-6xl lg:text-7xl font-black text-gray-900 mb-5">
                    Tổng hợp N
                </h1>
                <p class="text-xl md:text-2xl text-gray-600 font-medium">
                    Chọn cấp độ phù hợp để bắt đầu hành trình của bạn
                </p>
            </div>
        </div>
    </section>

    <!-- Levels Section -->
    <section class="pb-20 -mt-8">
        <div class="container mx-auto max-w-5xl px-4 md:px-6">
            <div class="space-y-5">
                <!-- N5 -->
                <a href="{{ route('course.show', 'n5') }}" class="block group card-hover">
                    <div class="relative bg-gradient-to-r from-red-500 to-red-600 rounded-2xl p-6 md:p-8 shadow-xl overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
                        <div class="relative flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <div class="w-20 h-20 bg-white rounded-xl flex items-center justify-center shadow-xl flex-shrink-0">
                                    <span class="text-3xl font-black text-red-600">N5</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-3xl">🌱</span>
                                        <h3 class="text-2xl md:text-3xl font-bold text-white">Sơ cấp</h3>
                                    </div>
                                    <p class="text-white/90 text-base md:text-lg font-medium">Beginner - Dành cho người mới bắt đầu</p>
                                </div>
                            </div>
                            <div class="hidden md:flex items-center gap-3 text-white font-semibold text-lg">
                                <span>Bắt đầu</span>
                                <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- N4 -->
                <a href="{{ route('course.show', 'n4') }}" class="block group card-hover">
                    <div class="relative bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-2xl p-6 md:p-8 shadow-xl overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
                        <div class="relative flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <div class="w-20 h-20 bg-white rounded-xl flex items-center justify-center shadow-xl flex-shrink-0">
                                    <span class="text-3xl font-black text-yellow-600">N4</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-3xl">📚</span>
                                        <h3 class="text-2xl md:text-3xl font-bold text-white">Trung cấp</h3>
                                    </div>
                                    <p class="text-white/90 text-base md:text-lg font-medium">Intermediate - Nâng cao kỹ năng tiếng Nhật</p>
                                </div>
                            </div>
                            <div class="hidden md:flex items-center gap-3 text-white font-semibold text-lg">
                                <span>Học ngay</span>
                                <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- N3 -->
                <a href="{{ route('course.show', 'n3') }}" class="block group card-hover">
                    <div class="relative bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl p-6 md:p-8 shadow-xl overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
                        <div class="relative flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <div class="w-20 h-20 bg-white rounded-xl flex items-center justify-center shadow-xl flex-shrink-0">
                                    <span class="text-3xl font-black text-blue-600">N3</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-3xl">💬</span>
                                        <h3 class="text-2xl md:text-3xl font-bold text-white">Trung cao</h3>
                                    </div>
                                    <p class="text-white/90 text-base md:text-lg font-medium">Upper Intermediate - Nâng cấp trình độ giao tiếp</p>
                                </div>
                            </div>
                            <div class="hidden md:flex items-center gap-3 text-white font-semibold text-lg">
                                <span>Khám phá</span>
                                <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- N2 -->
                <a href="{{ route('course.show', 'n2') }}" class="block group card-hover">
                    <div class="relative bg-gradient-to-r from-green-500 to-green-600 rounded-2xl p-6 md:p-8 shadow-xl overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
                        <div class="relative flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <div class="w-20 h-20 bg-white rounded-xl flex items-center justify-center shadow-xl flex-shrink-0">
                                    <span class="text-3xl font-black text-green-600">N2</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-3xl">🎌</span>
                                        <h3 class="text-2xl md:text-3xl font-bold text-white">Cao cấp</h3>
                                    </div>
                                    <p class="text-white/90 text-base md:text-lg font-medium">Advanced - Thành thạo tiếng Nhật</p>
                                </div>
                            </div>
                            <div class="hidden md:flex items-center gap-3 text-white font-semibold text-lg">
                                <span>Tiếp tục</span>
                                <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>

                <!-- N1 -->
                <a href="{{ route('course.show', 'n1') }}" class="block group card-hover">
                    <div class="relative bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl p-6 md:p-8 shadow-xl overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-20 -mt-20"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-white/5 rounded-full -ml-16 -mb-16"></div>
                        <div class="relative flex items-center justify-between">
                            <div class="flex items-center gap-6">
                                <div class="w-20 h-20 bg-white rounded-xl flex items-center justify-center shadow-xl flex-shrink-0">
                                    <span class="text-3xl font-black text-purple-600">N1</span>
                                    <span class="absolute text-xs font-bold text-purple-600 -top-1 -right-1 bg-white rounded-full w-6 h-6 flex items-center justify-center border-2 border-purple-600">🏆</span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-3xl">🏆</span>
                                        <h3 class="text-2xl md:text-3xl font-bold text-white">Thành thạo</h3>
                                    </div>
                                    <p class="text-white/90 text-base md:text-lg font-medium">Master - Bậc thầy tiếng Nhật</p>
                                </div>
                            </div>
                            <div class="hidden md:flex items-center gap-3 text-white font-semibold text-lg">
                                <span>Đỉnh cao</span>
                                <svg class="w-6 h-6 group-hover:translate-x-2 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white">
        <div class="container mx-auto max-w-6xl px-4 md:px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-red-50 to-red-100 border border-red-200">
                    <div class="text-5xl font-black text-red-600 mb-3">5</div>
                    <div class="text-lg font-bold text-gray-900 mb-1">Cấp độ</div>
                    <div class="text-sm text-gray-600">Từ N5 đến N1</div>
                </div>
                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200">
                    <div class="text-5xl font-black text-blue-600 mb-3">100+</div>
                    <div class="text-lg font-bold text-gray-900 mb-1">Bài học</div>
                    <div class="text-sm text-gray-600">Nội dung phong phú</div>
                </div>
                <div class="text-center p-8 rounded-2xl bg-gradient-to-br from-green-50 to-green-100 border border-green-200">
                    <div class="text-5xl font-black text-green-600 mb-3">24K+</div>
                    <div class="text-lg font-bold text-gray-900 mb-1">Từ vựng</div>
                    <div class="text-sm text-gray-600">Đầy đủ từ cơ bản</div>
                </div>
            </div>
        </div>
    </section>

    @include('layouts.footer')
</body>
</html>
