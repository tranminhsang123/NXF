@extends('adminlayout.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Lịch sử phiên bản</h1>
        <p class="text-gray-600 mt-2">{{ $title }}</p>
    </div>
    <a href="{{ route('admin.content-ops.preview', ['type' => $type, 'id' => $item->id]) }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Quay lại xem trước</a>
</div>

@if(session('success'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thời gian</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người thao tác</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Thay đổi</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Khôi phục</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($versions as $version)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $version->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ ['created' => 'Tạo mới', 'updated' => 'Cập nhật', 'deleted' => 'Xóa', 'restored' => 'Khôi phục'][$version->action] ?? $version->action }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $version->actor?->name ?? 'Hệ thống' }}</td>
                        <td class="px-6 py-4 text-xs text-gray-700">
                            @if($version->changes)
                                <pre class="max-h-40 overflow-auto rounded bg-gray-50 p-2">{{ json_encode($version->changes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @adminCan('content_ops.edit')
                                <form method="POST" action="{{ route('admin.content-ops.restore', $version) }}" onsubmit="return confirm('Khôi phục phiên bản này?')">
                                    @csrf
                                    <button class="rounded bg-amber-500 px-3 py-1 text-xs font-semibold text-white hover:bg-amber-600">Khôi phục</button>
                                </form>
                            @endadminCan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">Chưa có phiên bản.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($versions->hasPages())
        <div class="border-t border-gray-200 px-6 py-4">{{ $versions->links() }}</div>
    @endif
</div>
@endsection
