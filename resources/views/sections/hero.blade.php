<!-- Hero Section -->
<section class="px-4 pb-10 pt-8 sm:px-6 sm:pb-12 sm:pt-10 md:pb-20 md:pt-16 lg:pt-24 pattern-bg">
    <div class="container mx-auto max-w-6xl">
        <div class="grid items-center gap-8 md:grid-cols-2 lg:gap-12">
            <div class="mx-auto max-w-xl text-center md:mx-0 md:text-left">
                <div class="mb-4 inline-block rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-600 md:px-4 md:py-2 md:text-sm">
                    ✨ Phương pháp học hiện đại
                </div>
                <h2 class="mb-4 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl md:mb-6 md:text-5xl lg:text-6xl">
                    Bắt đầu học
                    <span class="text-gradient block">Tiếng Nhật</span>
                </h2>
                <p class="mb-2 text-[15px] text-gray-600 sm:text-base md:text-xl">今日から始めましょう (Hôm nay hãy bắt đầu)</p>
                <p class="mx-auto mb-6 max-w-lg text-sm leading-6 text-gray-500 md:mx-0 md:mb-8 md:text-lg md:leading-8">
                    Nền tảng học tiếng Nhật toàn diện với phương pháp khoa học, 
                    giúp bạn thành thạo từ N5 đến N1 trong thời gian ngắn nhất
                </p>
                
                <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap md:gap-4">
                    <a href="{{ route('minna.index') }}"
                       class="hero-cta-main flex min-h-12 w-full items-center justify-center rounded-xl bg-red-600 px-6 py-3 text-center text-base font-bold text-white shadow-xl transition hover:bg-red-700 sm:w-auto sm:rounded-full md:px-8 md:py-4 md:text-lg">
                        Bắt đầu với Minna
                    </a>
                    <a href="{{ route('alphabet.index') }}"
                       class="flex min-h-12 w-full items-center justify-center rounded-xl border-2 border-red-600 px-6 py-3 text-center text-base font-bold text-red-600 transition hover:bg-red-50 sm:w-auto sm:rounded-full md:px-8 md:py-4 md:text-lg">
                        Học bảng chữ cái
                    </a>
                </div>
                
                <div class="mt-6 grid grid-cols-2 gap-3 md:mt-8 md:flex md:items-center md:gap-6">
                    <div class="flex items-center justify-center rounded-2xl border border-red-100 bg-white/80 px-3 py-3 shadow-sm md:justify-start md:border-0 md:bg-transparent md:p-0 md:shadow-none">
                        <div class="flex -space-x-2">
                            <div class="h-9 w-9 rounded-full border-2 border-white bg-gradient-to-br from-red-400 to-red-600 md:h-10 md:w-10"></div>
                            <div class="h-9 w-9 rounded-full border-2 border-white bg-gradient-to-br from-yellow-400 to-yellow-600 md:h-10 md:w-10"></div>
                            <div class="h-9 w-9 rounded-full border-2 border-white bg-gradient-to-br from-blue-400 to-blue-600 md:h-10 md:w-10"></div>
                        </div>
                        <div class="ml-2 text-left">
                            <p class="font-semibold text-gray-900">+15,000</p>
                            <p class="text-sm text-gray-500">Học viên</p>
                        </div>
                    </div>
                    <div class="rounded-2xl border border-yellow-100 bg-white/80 px-3 py-3 text-center shadow-sm md:border-0 md:bg-transparent md:p-0 md:shadow-none">
                        <div class="flex items-center justify-center space-x-1 md:justify-start">
                            <span class="text-yellow-500">★★★★★</span>
                        </div>
                        <p class="text-sm text-gray-500">4.9/5 (1,234 đánh giá)</p>
                    </div>
                </div>
            </div>
            
            <div class="hero-card-float relative mx-auto mt-4 w-full max-w-sm md:mt-0 md:max-w-none">
                <div class="absolute inset-0 hidden rounded-3xl bg-gradient-to-br from-red-100 to-yellow-100 sm:block sm:rotate-3"></div>
                <div class="card-hover relative rounded-2xl bg-white p-4 shadow-xl sm:p-5 md:rounded-3xl md:p-8 md:shadow-2xl">
                    <div class="space-y-4 md:space-y-6">
                        <div class="rounded-2xl border border-red-100 bg-red-50 p-4 md:p-6">
                            <div class="mb-3 flex items-start justify-between gap-3">
                                <h3 class="min-w-0 text-sm font-bold leading-snug text-gray-900 md:text-base">今日の単語 - Từ vựng hôm nay</h3>
                                <span class="shrink-0 text-xl md:text-2xl">📚</span>
                            </div>
                            <div class="mb-2 text-2xl font-bold md:text-3xl">こんにちは</div>
                            <div class="mb-2 text-base text-gray-600 md:text-lg">Konnichiwa</div>
                            <div class="text-xs text-gray-500 md:text-sm">Xin chào (dùng buổi trưa/tối)</div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 md:gap-4">
                            <div class="rounded-xl border border-yellow-100 bg-yellow-50 p-3 md:p-4">
                                <div class="mb-2 text-2xl font-bold text-yellow-600 md:text-3xl">85%</div>
                                <div class="text-xs text-gray-600 md:text-sm">Hoàn thành khóa</div>
                            </div>
                            <div class="rounded-xl border border-blue-100 bg-blue-50 p-3 md:p-4">
                                <div class="mb-2 text-2xl font-bold text-blue-600 md:text-3xl">N3</div>
                                <div class="text-xs text-gray-600 md:text-sm">Trình độ hiện tại</div>
                            </div>
                        </div>
                        
                        <div class="rounded-xl bg-gradient-to-r from-red-500 to-yellow-500 p-3 text-white md:p-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <div class="text-xs opacity-90 md:text-sm">Đã học hôm nay</div>
                                    <div class="text-xl font-bold md:text-2xl">127 từ</div>
                                </div>
                                <div class="shrink-0 text-3xl md:text-4xl">🔥</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
