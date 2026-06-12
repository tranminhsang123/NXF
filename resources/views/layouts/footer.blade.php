@include('components.study-assist')

<!-- Footer -->
<footer class="relative bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 text-gray-300 mt-auto w-full overflow-hidden">
    <!-- Decorative background pattern -->
    <div class="absolute inset-0 opacity-5">
        <div class="absolute inset-0" style="background-image: radial-gradient(circle at 2px 2px, white 1px, transparent 0); background-size: 40px 40px;"></div>
    </div>
    
    <div class="relative container mx-auto max-w-7xl px-4 py-10 sm:px-6 md:py-16 lg:px-8">
        <div class="mb-10 grid grid-cols-1 gap-8 md:grid-cols-2 md:gap-10 lg:grid-cols-4 lg:gap-12">
            <!-- Brand Section -->
            <div class="lg:col-span-1">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-red-600 to-red-700 rounded-xl flex items-center justify-center shadow-lg transform hover:scale-110 transition-transform duration-300">
                            <span class="text-white text-2xl font-bold">日</span>
                        </div>
                        <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full animate-pulse"></div>
                    </div>
                    <h3 class="text-2xl font-bold bg-gradient-to-r from-white to-gray-300 bg-clip-text text-transparent">
                        日本語
                    </h3>
                </div>
                <p class="text-gray-400 mb-6 leading-relaxed">
                    Học tiếng Nhật hiệu quả và thú vị nhất. Khám phá văn hóa Nhật Bản qua từng bài học.
                </p>
                <div class="flex flex-wrap gap-3">
                    @forelse(($footerSocialLinks ?? collect()) as $socialLink)
                        <a
                            href="{{ $socialLink->url }}"
                            class="group relative flex h-11 w-11 items-center justify-center rounded-full bg-gray-800 transition-all duration-300 hover:scale-110 hover:bg-gradient-to-br hover:shadow-lg {{ $socialLink->hoverClasses() }}"
                            title="{{ $socialLink->label }}"
                            aria-label="{{ $socialLink->label }}"
                            @if($socialLink->isExternalUrl()) target="_blank" rel="noopener noreferrer" @endif
                        >
                            <span class="text-gray-400 transition-colors group-hover:text-white">
                                @include('components.social-icon', ['platform' => $socialLink->platform, 'class' => 'h-5 w-5'])
                            </span>
                        </a>
                    @empty
                        <span class="text-sm text-gray-500">Chưa cấu hình mạng xã hội.</span>
                    @endforelse
                </div>
            </div>
            
            <!-- Khóa học Section -->
            <div>
                <h4 class="text-white font-bold text-lg mb-6 relative inline-block">
                    Khóa học
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-red-600 to-transparent"></span>
                </h4>
                <ul class="space-y-3">
                    <li>
                        <a href="#" class="group flex items-center text-gray-400 hover:text-white transition-all duration-300 hover:translate-x-1">
                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                            <span>N5 - Sơ cấp</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="group flex items-center text-gray-400 hover:text-white transition-all duration-300 hover:translate-x-1">
                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                            <span>N4 - Trung cấp</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="group flex items-center text-gray-400 hover:text-white transition-all duration-300 hover:translate-x-1">
                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                            <span>N3 - Trung cao cấp</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="group flex items-center text-gray-400 hover:text-white transition-all duration-300 hover:translate-x-1">
                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                            <span>N2/N1 - Cao cấp</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Hỗ trợ Section -->
            <div>
                <h4 class="text-white font-bold text-lg mb-6 relative inline-block">
                    Hỗ trợ
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-red-600 to-transparent"></span>
                </h4>
                <ul class="space-y-3">
                    <li>
                        <a href="#" class="group flex items-center text-gray-400 hover:text-white transition-all duration-300 hover:translate-x-1">
                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                            <span>Liên hệ</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="group flex items-center text-gray-400 hover:text-white transition-all duration-300 hover:translate-x-1">
                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                            <span>Câu hỏi thường gặp</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="group flex items-center text-gray-400 hover:text-white transition-all duration-300 hover:translate-x-1">
                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                            <span>Blog</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="group flex items-center text-gray-400 hover:text-white transition-all duration-300 hover:translate-x-1">
                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full mr-3 opacity-0 group-hover:opacity-100 transition-opacity"></span>
                            <span>Về chúng tôi</span>
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Newsletter Section -->
            <div>
                <h4 class="text-white font-bold text-lg mb-6 relative inline-block">
                    Đăng ký nhận tin
                    <span class="absolute bottom-0 left-0 w-full h-0.5 bg-gradient-to-r from-red-600 to-transparent"></span>
                </h4>
                <p class="text-gray-400 mb-4 text-sm leading-relaxed">
                    Nhận thông tin về khóa học mới và tips học tiếng Nhật hàng tuần.
                </p>
                <form class="space-y-3">
                    <input 
                        type="email" 
                        placeholder="Email của bạn" 
                        class="w-full px-4 py-3 bg-gray-800 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-transparent text-white placeholder-gray-500 transition-all"
                    >
                    <button 
                        type="submit" 
                        class="w-full px-6 py-3 bg-gradient-to-r from-red-600 to-red-700 text-white font-semibold rounded-lg hover:from-red-700 hover:to-red-800 transform hover:scale-105 transition-all duration-300 shadow-lg hover:shadow-xl"
                    >
                        Đăng ký
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Bottom Bar -->
        <div class="mt-8 border-t border-gray-700 pt-8">
            <div class="flex flex-col items-center justify-between gap-4 text-center md:flex-row md:text-left">
                <p class="text-sm text-gray-400">
                    &copy; {{ date('Y') }} <span class="text-white font-semibold">日本語</span> Học Tiếng Nhật. Tất cả quyền được bảo lưu.
                </p>
                <div class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm md:justify-end">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Chính sách bảo mật</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors duration-300">Điều khoản sử dụng</a>
                </div>
            </div>
        </div>
    </div>
</footer>
