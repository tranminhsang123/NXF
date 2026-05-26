@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Bảo mật / DevTools</h1>
    <p class="text-gray-600 mt-2">Cài đặt ghi log vi phạm (F12, Ctrl+Shift+I/J, Ctrl+U) và khóa tài khoản. Danh sách vi phạm gần đây.</p>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
@endif

<!-- Cài đặt -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Cài đặt</h2>
    <form action="{{ route('admin.security.update') }}" method="POST">
        @csrf
        <input type="hidden" name="devtools_log_enabled" value="0">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="devtools_log_enabled" value="1"
                           {{ $settings['devtools_log_enabled'] ? 'checked' : '' }}
                           class="rounded border-gray-300">
                    <span class="text-sm font-medium text-gray-700">Ghi log vi phạm DevTools</span>
                </label>
                <p class="text-xs text-gray-500 mt-1">Khi bật, mỗi lần user bấm F12 / Ctrl+Shift+I/J / Ctrl+U sẽ ghi vào bảng bên dưới.</p>
            </div>
            <div>
                <label for="lock_after" class="block text-sm font-medium text-gray-700 mb-2">Khóa tài khoản sau số lần vi phạm</label>
                <input type="number" id="lock_after" name="devtools_lock_after_violations"
                       value="{{ $settings['devtools_lock_after_violations'] }}"
                       min="0" max="100" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">0 = không tự động khóa. 1 = khóa ngay lần đầu.</p>
            </div>
            <div>
                <label for="window_hours" class="block text-sm font-medium text-gray-700 mb-2">Cửa sổ tính vi phạm (giờ)</label>
                <input type="number" id="window_hours" name="devtools_violation_window_hours"
                       value="{{ $settings['devtools_violation_window_hours'] }}"
                       min="1" max="720" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">Chỉ đếm vi phạm trong X giờ gần nhất để quyết định khóa.</p>
            </div>
        </div>
        <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="md:col-span-2">
                <label for="lock_message" class="block text-sm font-medium text-gray-700 mb-2">Thông báo gửi cho user khi bị khóa</label>
                <textarea id="lock_message" name="devtools_lock_message" rows="3" maxlength="500"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2"
                          placeholder="Nội dung hiển thị khi user đăng nhập bị chặn (để trống = giữ nguyên)">{{ $settings['devtools_lock_message'] ?? '' }}</textarea>
                <p class="text-xs text-gray-500 mt-1">Tối đa 500 ký tự. Hiển thị tại form đăng nhập và khi session bị đăng xuất do khóa.</p>
            </div>
            <div>
                <label for="auto_unlock_hours" class="block text-sm font-medium text-gray-700 mb-2">Tự mở khóa sau (giờ)</label>
                <input type="number" id="auto_unlock_hours" name="devtools_auto_unlock_hours"
                       value="{{ $settings['devtools_auto_unlock_hours'] ?? 0 }}"
                       min="0" max="720" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <p class="text-xs text-gray-500 mt-1">0 = không tự mở. &gt;0 = soft-ban: sau X giờ tài khoản tự mở khóa.</p>
            </div>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Lưu cài đặt</button>
        </div>
    </form>
</div>

<!-- Giới hạn tính năng khi chưa đăng nhập -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-8">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Giới hạn trải nghiệm cho khách (chưa đăng nhập)</h2>
    <p class="text-sm text-gray-600 mb-4">
        Khi bật "Yêu cầu đăng nhập", user chưa đăng nhập sẽ bị chặn truy cập tính năng tương ứng.
        User đã đăng nhập luôn dùng đầy đủ tính năng.
    </p>
    <form action="{{ route('admin.security.update') }}" method="POST">
        @csrf
        <div class="space-y-3">
            <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50">
                <input type="hidden" name="feature_lock_alphabet" value="0">
                <input type="checkbox" name="feature_lock_alphabet" value="1" class="mt-1 rounded border-gray-300"
                       {{ ($featureLocks['alphabet'] ?? true) ? 'checked' : '' }}>
                <span>
                    <span class="block text-sm font-medium text-gray-800">Bảng chữ cái</span>
                    <span class="block text-xs text-gray-500">Route: /alphabet</span>
                </span>
            </label>

            <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50">
                <input type="hidden" name="feature_lock_kanji" value="0">
                <input type="checkbox" name="feature_lock_kanji" value="1" class="mt-1 rounded border-gray-300"
                       {{ ($featureLocks['kanji'] ?? true) ? 'checked' : '' }}>
                <span>
                    <span class="block text-sm font-medium text-gray-800">Ôn Kanji</span>
                    <span class="block text-xs text-gray-500">Route: /kanji/*</span>
                </span>
            </label>

            <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50">
                <input type="hidden" name="feature_lock_flashcard" value="0">
                <input type="checkbox" name="feature_lock_flashcard" value="1" class="mt-1 rounded border-gray-300"
                       {{ ($featureLocks['flashcard'] ?? true) ? 'checked' : '' }}>
                <span>
                    <span class="block text-sm font-medium text-gray-800">Flashcard</span>
                    <span class="block text-xs text-gray-500">Route: /flashcard*</span>
                </span>
            </label>

            <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50">
                <input type="hidden" name="feature_lock_vocabulary" value="0">
                <input type="checkbox" name="feature_lock_vocabulary" value="1" class="mt-1 rounded border-gray-300"
                       {{ ($featureLocks['vocabulary'] ?? true) ? 'checked' : '' }}>
                <span>
                    <span class="block text-sm font-medium text-gray-800">Từ vựng</span>
                    <span class="block text-xs text-gray-500">Route: /tu-vung*</span>
                </span>
            </label>

            <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50">
                <input type="hidden" name="feature_lock_course" value="0">
                <input type="checkbox" name="feature_lock_course" value="1" class="mt-1 rounded border-gray-300"
                       {{ ($featureLocks['course'] ?? true) ? 'checked' : '' }}>
                <span>
                    <span class="block text-sm font-medium text-gray-800">Khóa học JLPT</span>
                    <span class="block text-xs text-gray-500">Route: /courses, /course/*</span>
                </span>
            </label>

            <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-3 hover:bg-gray-50">
                <input type="hidden" name="feature_lock_minna" value="0">
                <input type="checkbox" name="feature_lock_minna" value="1" class="mt-1 rounded border-gray-300"
                       {{ ($featureLocks['minna'] ?? true) ? 'checked' : '' }}>
                <span>
                    <span class="block text-sm font-medium text-gray-800">Minna no Nihongo</span>
                    <span class="block text-xs text-gray-500">Route: /minna/*</span>
                </span>
            </label>
        </div>
        <div class="mt-6">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">Lưu giới hạn trải nghiệm</button>
        </div>
    </form>
</div>

<!-- Danh sách vi phạm -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <h2 class="text-xl font-bold text-gray-900 p-6 pb-0">Lịch sử vi phạm DevTools</h2>
    <div class="overflow-x-auto">
        <table class="w-full mt-4">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại vi phạm</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($violations as $v)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $v->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($v->user)
                            <a href="{{ route('admin.users.edit', $v->user) }}" class="text-indigo-600 hover:underline">{{ $v->user->name }}</a>
                            <span class="text-gray-500 text-xs block">{{ $v->user->email }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ \App\Models\DevtoolsViolation::typeLabel($v->violation_type) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $v->ip_address ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Chưa có bản ghi vi phạm.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($violations->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">{{ $violations->links() }}</div>
    @endif
</div>
@endsection
