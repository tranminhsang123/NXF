<!-- CTA Section -->
<section class="bg-gradient-to-r from-red-600 to-yellow-500 py-10 sm:py-12 md:py-20">
    <div class="container mx-auto max-w-4xl px-4 text-center sm:px-6">
        <h2 class="mb-4 text-2xl font-bold leading-tight text-white md:mb-6 md:text-4xl lg:text-5xl">
            Sẵn sàng chinh phục tiếng Nhật?
        </h2>
        <p class="mx-auto mb-6 max-w-2xl text-sm leading-6 text-white/90 sm:text-base md:mb-8 md:text-xl">
            Chọn lộ trình phù hợp: bắt đầu với Minna no Nihongo hoặc luyện bảng chữ cái trước.
        </p>
        <div class="flex flex-col justify-center gap-3 sm:flex-row md:gap-4">
            <a href="{{ route('minna.index') }}"
               class="flex min-h-12 w-full items-center justify-center rounded-xl bg-white px-8 py-3 text-center text-base font-bold text-red-600 shadow-xl transition hover:bg-gray-100 sm:w-auto sm:rounded-full md:px-12 md:py-4 md:text-xl">
                Vào bài Minna đầu tiên
            </a>
            <a href="{{ route('alphabet.index') }}"
               class="flex min-h-12 w-full items-center justify-center rounded-xl border border-white/40 bg-red-500/10 px-8 py-3 text-center text-base font-semibold text-white transition hover:bg-red-500/20 sm:w-auto sm:rounded-full md:px-12 md:py-4 md:text-lg">
                Luyện bảng chữ cái
            </a>
        </div>
    </div>
</section>
