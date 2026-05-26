<!-- CTA Section -->
<section class="py-12 md:py-20 bg-gradient-to-r from-red-600 to-yellow-500">
    <div class="container mx-auto max-w-4xl px-4 md:px-6 text-center">
        <h2 class="text-2xl md:text-4xl lg:text-5xl font-bold text-white mb-4 md:mb-6">
            Sẵn sàng chinh phục tiếng Nhật?
        </h2>
        <p class="text-base md:text-xl text-white/90 mb-6 md:mb-8">
            Chọn lộ trình phù hợp: bắt đầu với Minna no Nihongo hoặc luyện bảng chữ cái trước.
        </p>
        <div class="flex flex-col sm:flex-row justify-center gap-3 md:gap-4">
            <a href="{{ route('minna.index') }}"
               class="bg-white text-red-600 px-8 md:px-12 py-3 md:py-4 rounded-full font-bold text-base md:text-xl hover:bg-gray-100 transition transform hover:scale-105 shadow-2xl w-full sm:w-auto text-center">
                Vào bài Minna đầu tiên
            </a>
            <a href="{{ route('alphabet.index') }}"
               class="bg-red-500/10 text-white border border-white/40 px-8 md:px-12 py-3 md:py-4 rounded-full font-semibold text-base md:text-lg hover:bg-red-500/20 transition w-full sm:w-auto text-center">
                Luyện bảng chữ cái
            </a>
        </div>
    </div>
</section>
