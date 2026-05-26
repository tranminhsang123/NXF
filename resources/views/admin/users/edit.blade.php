@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Sửa User</h1>
        <a href="{{ route('admin.users.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-center">
            ← Quay lại
        </a>
    </div>
</div>

@if($errors->has('admin_role_ids'))
    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">{{ $errors->first('admin_role_ids') }}</div>
@endif

@if($user->isLocked())
    <div class="mb-6 bg-amber-50 border border-amber-200 rounded-lg p-4 flex items-center justify-between">
        <div>
            <span class="font-medium text-amber-800">Tài khoản đang bị khóa</span>
            @if($user->locked_reason)
                <p class="text-sm text-amber-700 mt-1">{{ $user->locked_reason }}</p>
            @endif
        </div>
        @adminCan('users.lock')
            @if($user->id !== auth()->id())
                <form action="{{ route('admin.users.unlock', $user) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700">Mở khóa</button>
                </form>
            @endif
        @endadminCan
    </div>
@elseif($user->id !== auth()->id())
    @adminCan('users.lock')
    <div class="mb-6 bg-gray-50 border border-gray-200 rounded-lg p-4 flex items-center justify-between">
        <span class="text-gray-700">Tài khoản đang hoạt động.</span>
        <form action="{{ route('admin.users.lock', $user) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-amber-600 text-white px-4 py-2 rounded-lg hover:bg-amber-700"
                    onclick="return confirm('Bạn có chắc muốn khóa tài khoản này?')">Khóa tài khoản</button>
        </form>
    </div>
    @endadminCan
@endif

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('admin.users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên *</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('name') border-red-500 @enderror" required>
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('email') border-red-500 @enderror" required>
                @error('email')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Vai trò *</label>
                <select id="role" name="role"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2
                               @error('role') border-red-500 @enderror" required>
                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.users.index') }}"
               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                Hủy
            </a>
            <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Cập nhật User
            </button>
        </div>
    </form>
</div>

@isset($learningProfile)
<div class="bg-white rounded-lg shadow-sm p-6 mt-6">
    <h2 class="text-lg font-bold text-gray-900 mb-4">Hồ sơ học tập chi tiết</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Streak / XP</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ (int) ($user->current_streak ?? 0) }} ngày</p>
            <p class="mt-1 text-xs text-gray-500">{{ (int) ($user->xp_total ?? 0) }} XP</p>
        </div>
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Bài đã hoàn thành</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $learningProfile['completed_lessons_count'] }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $learningProfile['in_progress_lessons_count'] }} bài đang học</p>
        </div>
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Quiz</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $learningProfile['quiz_average'] ?? '-' }}%</p>
            <p class="mt-1 text-xs text-gray-500">{{ $learningProfile['quiz_attempts'] }} lượt làm · tốt nhất {{ $learningProfile['quiz_best'] ?? '-' }}%</p>
        </div>
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Tương tác</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $learningProfile['favorite_count'] }} lưu</p>
            <p class="mt-1 text-xs text-gray-500">{{ $learningProfile['group_count'] }} nhóm · {{ $learningProfile['campaign_count'] }} campaign đã nhận</p>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <div>
            <div class="mb-3 flex items-center justify-between">
                <h3 class="font-semibold text-gray-900">Bài hoàn thành gần đây</h3>
                <span class="text-xs text-gray-500">Lần học cuối: {{ optional($learningProfile['last_activity_at'])->format('d/m/Y') ?? '-' }}</span>
            </div>
            <div class="space-y-2">
                @forelse($learningProfile['completed_lessons'] as $progress)
                    <div class="rounded-lg border border-gray-100 px-3 py-2 text-sm">
                        <span class="font-semibold text-gray-900">Bài {{ $progress->lesson?->number ?? '?' }}</span>
                        <span class="text-gray-600">- {{ $progress->lesson?->title ?? 'Không rõ' }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">User chưa hoàn thành bài nào.</p>
                @endforelse
            </div>
        </div>
        <div>
            <h3 class="mb-3 font-semibold text-gray-900">Quiz gần đây</h3>
            <div class="space-y-2">
                @forelse($learningProfile['recent_quiz_attempts'] as $attempt)
                    <div class="flex items-center justify-between rounded-lg border border-gray-100 px-3 py-2 text-sm">
                        <span class="font-semibold text-gray-900">Bài {{ $attempt->lesson?->number ?? '?' }}</span>
                        <span class="text-gray-600">{{ $attempt->percent }}%</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">User chưa làm quiz.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endisset

@if($user->role === 'admin')
<div class="bg-white rounded-lg shadow-sm p-6 mt-6">
    <h2 class="text-lg font-bold text-gray-900 mb-2">Vai trò admin (RBAC)</h2>
    <p class="text-sm text-gray-600 mb-4">Mỗi admin có thể có nhiều vai trò; quyền truy cập từng màn hình được kiểm tra theo bảng route → permission.</p>

    @if($user->adminRoles->isNotEmpty())
        <p class="text-sm text-gray-800 mb-4"><span class="font-medium">Đang gán:</span> {{ $user->adminRoles->pluck('name')->join(', ') }}</p>
    @else
        <p class="text-sm text-amber-800 mb-4">Chưa gán vai trò — hệ thống coi như <strong>full quyền</strong> (tương thích cũ). Nên gán ít nhất một vai trò sau khi chạy seeder RBAC.</p>
    @endif

    @adminCan('users.assign_roles')
        <form action="{{ route('admin.users.admin-roles.update', $user) }}" method="POST" class="space-y-3">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach($allAdminRoles as $role)
                    <label class="flex items-start gap-2 border border-gray-200 rounded-lg p-3 cursor-pointer hover:bg-gray-50">
                        <input type="checkbox" name="admin_role_ids[]" value="{{ $role->id }}" class="mt-1 rounded border-gray-300"
                               {{ $user->adminRoles->contains($role->id) ? 'checked' : '' }}>
                        <span>
                            <span class="font-medium text-gray-900">{{ $role->name }}</span>
                            <span class="block text-xs text-gray-500 font-mono">{{ $role->slug }}</span>
                        </span>
                    </label>
                @endforeach
            </div>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm font-semibold">
                Lưu vai trò RBAC
            </button>
        </form>
    @else
        <p class="text-sm text-gray-500">Bạn không có quyền gán vai trò (cần permission <code class="text-xs bg-gray-100 px-1 rounded">users.assign_roles</code>).</p>
    @endadminCan
</div>
@endif
@endsection
