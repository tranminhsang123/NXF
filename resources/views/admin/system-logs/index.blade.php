@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Log hệ thống</h1>
    <p class="text-gray-600 mt-2">Đăng nhập thất bại, đăng ký, khóa / mở khóa tài khoản.</p>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Loại</label>
            <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Từ ngày</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Đến ngày</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">Lọc</button>
            <a href="{{ route('admin.system-logs.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nội dung</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($logs as $log)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 text-xs font-medium rounded
                            @if($log->type === 'failed_login') bg-red-100 text-red-800
                            @elseif($log->type === 'user_registered') bg-green-100 text-green-800
                            @elseif($log->type === 'user_locked') bg-amber-100 text-amber-800
                            @elseif($log->type === 'user_unlocked' || $log->type === 'user_auto_unlocked') bg-blue-100 text-blue-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $types[$log->type] ?? $log->type }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($log->user)
                            <a href="{{ route('admin.users.edit', $log->user) }}" class="text-indigo-600 hover:underline">{{ $log->user->name }}</a>
                            <span class="text-gray-500 text-xs block">{{ $log->user->email }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-700">{{ $log->message }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Chưa có bản ghi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
