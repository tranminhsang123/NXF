@extends('adminlayout.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Vận hành nội dung</h1>
        <p class="text-gray-600 mt-2">Xem trước, kiểm tra, xuất bản và khôi phục nội dung học.</p>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
    @foreach($stats as $type => $row)
        <a href="{{ route('admin.content-ops.index', ['type' => $type]) }}" class="block rounded-lg border {{ $selectedType === $type ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-white' }} p-4 shadow-sm">
            <p class="font-bold text-gray-900">{{ $row['label'] }}</p>
            <div class="mt-3 grid grid-cols-3 gap-2 text-center text-xs">
                <span class="rounded bg-amber-100 px-2 py-1 text-amber-800">Nháp {{ $row['draft'] }}</span>
                <span class="rounded bg-green-100 px-2 py-1 text-green-800">Xuất bản {{ $row['published'] }}</span>
                <span class="rounded bg-gray-100 px-2 py-1 text-gray-700">Lưu trữ {{ $row['archived'] }}</span>
            </div>
        </a>
    @endforeach
</div>

<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Loại nội dung</label>
            <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                @foreach($types as $type => $entry)
                    <option value="{{ $type }}" @selected($selectedType === $type)>{{ $entry['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Trạng thái</label>
            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                @foreach($statuses as $status => $label)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button class="rounded-lg bg-gray-700 px-4 py-2 text-white hover:bg-gray-800">Lọc</button>
            <a href="{{ route('admin.content-ops.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-300">Đặt lại</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nội dung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cập nhật</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($items as $item)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ \App\Support\AdminContentRegistry::titleFor($item) }}</p>
                            <p class="text-xs text-gray-500">#{{ $item->id }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <form method="POST" action="{{ route('admin.content-ops.status', ['type' => $selectedType, 'id' => $item->id]) }}" class="flex items-center gap-2">
                                @csrf
                                @method('PATCH')
                                <select name="publish_status" class="rounded-lg border border-gray-300 px-2 py-1 text-sm">
                                    @foreach($statuses as $status => $label)
                                        <option value="{{ $status }}" @selected(($item->publish_status ?? 'published') === $status)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @adminCan('content_ops.edit')
                                    <button class="rounded bg-red-600 px-3 py-1 text-xs font-semibold text-white hover:bg-red-700">Lưu</button>
                                @endadminCan
                            </form>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->updated_at?->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-right text-sm">
                            <a href="{{ route('admin.content-ops.preview', ['type' => $selectedType, 'id' => $item->id]) }}" class="font-semibold text-blue-600 hover:underline">Xem trước</a>
                            <span class="mx-2 text-gray-300">|</span>
                            <a href="{{ route('admin.content-ops.versions', ['type' => $selectedType, 'id' => $item->id]) }}" class="font-semibold text-gray-700 hover:underline">Phiên bản</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">Chưa có nội dung.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
        <div class="border-t border-gray-200 px-6 py-4">{{ $items->links() }}</div>
    @endif
</div>
@endsection
