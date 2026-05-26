<!-- Hero Section -->
<section class="pt-20 md:pt-32 pb-12 md:pb-20 px-4 md:px-6 pattern-bg">
    <div class="container mx-auto max-w-6xl">
        <div class="grid md:grid-cols-2 gap-8 md:gap-12 items-center">
            <div>
                <div class="inline-block bg-red-50 text-red-600 px-3 md:px-4 py-1.5 md:py-2 rounded-full text-xs md:text-sm font-semibold mb-4">
                    ✨ Phương pháp học hiện đại
                </div>
                <h2 class="text-3xl md:text-5xl lg:text-6xl font-bold text-gray-900 leading-tight mb-4 md:mb-6">
                    Bắt đầu học
                    <span class="text-gradient block">Tiếng Nhật</span>
                </h2>
                <p class="text-base md:text-xl text-gray-600 mb-2">今日から始めましょう (Hôm nay hãy bắt đầu)</p>
                <p class="text-sm md:text-lg text-gray-500 mb-6 md:mb-8">
                    Nền tảng học tiếng Nhật toàn diện với phương pháp khoa học, 
                    giúp bạn thành thạo từ N5 đến N1 trong thời gian ngắn nhất
                </p>
                
                <div class="flex flex-col sm:flex-row flex-wrap gap-3 md:gap-4">
                    <a href="{{ route('minna.index') }}"
                       class="hero-cta-main bg-red-600 text-white px-6 md:px-8 py-3 md:py-4 rounded-full font-bold text-base md:text-lg hover:bg-red-700 transition transform hover:scale-105 shadow-xl w-full sm:w-auto text-center">
                        Bắt đầu với Minna
                    </a>
                    <a href="{{ route('alphabet.index') }}"
                       class="border-2 border-red-600 text-red-600 px-6 md:px-8 py-3 md:py-4 rounded-full font-bold text-base md:text-lg hover:bg-red-50 transition w-full sm:w-auto text-center">
                        Học bảng chữ cái
                    </a>
                </div>
                
                <div class="mt-6 md:mt-8 flex flex-col sm:flex-row items-start sm:items-center space-y-4 sm:space-y-0 sm:space-x-6">
                    <div class="flex items-center space-x-2">
                        <div class="flex -space-x-2">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-400 to-red-600 border-2 border-white"></div>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-yellow-400 to-yellow-600 border-2 border-white"></div>
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 border-2 border-white"></div>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900">+15,000</p>
                            <p class="text-sm text-gray-500">Học viên</p>
                        </div>
                    </div>
                    <div class="text-center">
                        <div class="flex items-center space-x-1">
                            <span class="text-yellow-500">★★★★★</span>
                        </div>
                        <p class="text-sm text-gray-500">4.9/5 (1,234 đánh giá)</p>
                    </div>
                </div>
            </div>
            
            <div class="relative mt-8 md:mt-0 hero-card-float">
                <div class="absolute inset-0 bg-gradient-to-br from-red-100 to-yellow-100 rounded-3xl transform rotate-3"></div>
                <div class="relative bg-white p-4 md:p-8 rounded-3xl shadow-2xl card-hover">
                    <div class="space-y-4 md:space-y-6">
                        <div class="bg-red-50 p-4 md:p-6 rounded-2xl border border-red-100">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="font-bold text-gray-900 text-sm md:text-base">今日の単語 - Từ vựng hôm nay</h3>
                                <span class="text-xl md:text-2xl">📚</span>
                            </div>
                            <div class="text-2xl md:text-3xl font-bold mb-2">こんにちは</div>
                            <div class="text-base md:text-lg text-gray-600 mb-2">Konnichiwa</div>
                            <div class="text-xs md:text-sm text-gray-500">Xin chào (dùng buổi trưa/tối)</div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-3 md:gap-4">
                            <div class="bg-yellow-50 p-3 md:p-4 rounded-xl border border-yellow-100">
                                <div class="text-2xl md:text-3xl font-bold text-yellow-600 mb-2">85%</div>
                                <div class="text-xs md:text-sm text-gray-600">Hoàn thành khóa</div>
                            </div>
                            <div class="bg-blue-50 p-3 md:p-4 rounded-xl border border-blue-100">
                                <div class="text-2xl md:text-3xl font-bold text-blue-600 mb-2">N3</div>
                                <div class="text-xs md:text-sm text-gray-600">Trình độ hiện tại</div>
                            </div>
                        </div>
                        
                        <div class="bg-gradient-to-r from-red-500 to-yellow-500 text-white p-3 md:p-4 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-xs md:text-sm opacity-90">Đã học hôm nay</div>
                                    <div class="text-xl md:text-2xl font-bold">127 từ</div>
                                </div>
                                <div class="text-3xl md:text-4xl">🔥</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
