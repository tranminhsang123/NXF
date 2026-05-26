@extends('adminlayout.app')

@section('content')
@php
    $actionLabels = [
        'created' => 'Tạo mới',
        'updated' => 'Cập nhật',
        'deleted' => 'Xóa',
        'growth_campaign_created' => 'Tạo chiến dịch tăng trưởng',
        'growth_campaign_sent' => 'Gửi chiến dịch tăng trưởng',
    ];
@endphp

<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Nhật ký thao tác admin</h1>
    <p class="text-gray-600 mt-2">Theo dõi thao tác admin, thay đổi nội dung, gửi chiến dịch và khôi phục phiên bản.</p>
</div>

<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <select name="action" class="rounded-lg border border-gray-300 px-3 py-2">
            <option value="">Tất cả thao tác</option>
            @foreach($actions as $action)
                <option value="{{ $action }}" @selected(request('action') === $action)>{{ $actionLabels[$action] ?? $action }}</option>
            @endforeach
        </select>
        <input type="number" name="actor_id" value="{{ request('actor_id') }}" placeholder="ID người thao tác" class="rounded-lg border border-gray-300 px-3 py-2">
        <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-lg border border-gray-300 px-3 py-2">
        <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-lg border border-gray-300 px-3 py-2">
        <div class="flex gap-2">
            <button class="rounded-lg bg-gray-700 px-4 py-2 text-white hover:bg-gray-800">Lọc</button>
            <a href="{{ route('admin.audit-logs.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">Đặt lại</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người thao tác</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tóm tắt</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thay đổi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($logs as $log)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $log->created_at?->format('d/m/Y H:i:s') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $log->actor?->name ?? 'Hệ thống' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $actionLabels[$log->action] ?? $log->action }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $log->summary }}</td>
                        <td class="px-6 py-4 text-xs text-gray-700">
                            @if($log->before || $log->after)
                                <details>
                                    <summary class="cursor-pointer font-semibold text-blue-700">Xem</summary>
                                    <pre class="mt-2 max-h-60 overflow-auto rounded bg-gray-50 p-2">{{ json_encode(['before' => $log->before, 'after' => $log->after], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </details>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Chưa có nhật ký thao tác.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="border-t border-gray-200 px-6 py-4">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
