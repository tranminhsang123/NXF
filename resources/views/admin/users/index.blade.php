@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Quản lý Users</h1>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">{{ session('error') }}</div>
@endif

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Vai trò</label>
            <select name="role" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái khóa</label>
            <select name="locked_status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                <option value="locked" {{ request('locked_status') == 'locked' ? 'selected' : '' }}>Đang khóa</option>
                <option value="unlocked" {{ request('locked_status') == 'unlocked' ? 'selected' : '' }}>Không khóa</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Segment học</label>
            <select name="learning_segment" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                @foreach($segmentDefinitions ?? [] as $key => $definition)
                    <option value="{{ $key }}" {{ request('learning_segment') == $key ? 'selected' : '' }}>{{ $definition['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ngày tạo từ</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Ngày tạo đến</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div class="flex flex-col gap-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">&nbsp;</label>
            <div class="flex gap-2">
                <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Lọc</button>
                <a href="{{ route('admin.users.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Reset</a>
            </div>
        </div>
        <div class="lg:col-span-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm (tên / email)</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Tên hoặc email..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
    </form>
</div>

<!-- Users Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full min-w-[1080px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tên</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vai trò</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Học tập</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ngày tạo</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($users as $user)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">#{{ $user->id }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                    @if($user->isLocked())
                        <span class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded bg-amber-100 text-amber-800">Đã khóa</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-600">{{ $user->email }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $user->role == 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ (int) ($user->current_streak ?? 0) }} streak</div>
                    <div class="text-xs text-gray-500">Lần học cuối: {{ $user->last_study_date?->format('d/m/Y') ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-600">{{ $user->created_at->format('d/m/Y') }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex flex-wrap items-center gap-2">
                        @adminCan('users.edit')
                        <a href="{{ route('admin.users.edit', $user) }}"
                           class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                        @endadminCan
                        @if($user->id !== auth()->id())
                            @adminCan('users.lock')
                            @if($user->isLocked())
                                <form action="{{ route('admin.users.unlock', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-amber-600 hover:text-amber-800">Mở khóa</button>
                                </form>
                            @else
                                <form action="{{ route('admin.users.lock', $user) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-amber-600 hover:text-amber-800"
                                            onclick="return confirm('Bạn có chắc muốn khóa tài khoản này?')">Khóa</button>
                                </form>
                            @endif
                            @endadminCan
                            @adminCan('users.delete')
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900"
                                        onclick="return confirm('Bạn có chắc muốn xóa user này?')">Xóa</button>
                            </form>
                            @endadminCan
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <div class="text-lg">Chưa có user nào</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<!-- Pagination -->
@if($users->hasPages())
<div class="mt-6">
    {{ $users->appends(request()->query())->links() }}
</div>
@endif
@endsection
