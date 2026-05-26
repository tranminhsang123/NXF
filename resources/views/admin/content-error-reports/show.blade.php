@extends('adminlayout.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Xử lý báo lỗi #{{ $report->id }}</h1>
        <p class="text-gray-600 mt-2">{{ $report->content_title ?: 'Trang học' }} - {{ $report->categoryLabel() }}</p>
    </div>
    <a href="{{ route('admin.content-reports.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Quay lại</a>
</div>

@if(session('success'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Nội dung báo lỗi</h2>
            <dl class="divide-y divide-gray-100 text-sm">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 py-3">
                    <dt class="font-semibold text-gray-600">Người gửi</dt>
                    <dd class="md:col-span-3 text-gray-900">{{ $report->user?->name }} - {{ $report->user?->email }}</dd>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 py-3">
                    <dt class="font-semibold text-gray-600">Trang</dt>
                    <dd class="md:col-span-3 break-all">
                        @if($report->page_url)
                            <a href="{{ $report->page_url }}" target="_blank" class="text-red-600 hover:text-red-700">{{ $report->page_url }}</a>
                        @else
                            <span class="text-gray-500">Không có</span>
                        @endif
                    </dd>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 py-3">
                    <dt class="font-semibold text-gray-600">Đoạn bị lỗi</dt>
                    <dd class="md:col-span-3 whitespace-pre-wrap text-gray-900">{{ $report->selected_text ?: '-' }}</dd>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 py-3">
                    <dt class="font-semibold text-gray-600">Mô tả</dt>
                    <dd class="md:col-span-3 whitespace-pre-wrap text-gray-900">{{ $report->description }}</dd>
                </div>
            </dl>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Ngữ cảnh kỹ thuật</h2>
            <pre class="max-h-96 overflow-auto rounded bg-gray-50 p-4 text-xs">{{ json_encode($report->browser_context ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Cập nhật xử lý</h2>
        @adminCan('content_reports.manage')
            <form method="POST" action="{{ route('admin.content-reports.update', $report) }}" class="space-y-4">
                @csrf
                @method('PATCH')
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Trạng thái</label>
                    <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                        @foreach($statuses as $status => $label)
                            <option value="{{ $status }}" @selected($report->status === $status)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Gán cho admin</label>
                    <select name="assigned_to" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="">Chưa gán</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" @selected($report->assigned_to === $admin->id)>{{ $admin->name }} - {{ $admin->email }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Ghi chú xử lý</label>
                    <textarea name="resolution_note" rows="5" class="w-full rounded-lg border border-gray-300 px-3 py-2">{{ old('resolution_note', $report->resolution_note) }}</textarea>
                </div>
                <button class="w-full rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">Lưu xử lý</button>
            </form>
        @else
            <p class="text-sm text-gray-500">Bạn chỉ có quyền xem báo lỗi nội dung.</p>
        @endadminCan

        <div class="mt-6 rounded-lg bg-gray-50 p-4 text-sm">
            <p class="font-semibold text-gray-900">Thông tin hiện tại</p>
            <p class="mt-2 text-gray-600">Trạng thái: {{ $report->statusLabel() }}</p>
            <p class="text-gray-600">Người xử lý: {{ $report->resolver?->name ?? '-' }}</p>
            <p class="text-gray-600">Ngày xử lý: {{ $report->resolved_at?->format('d/m/Y H:i') ?? '-' }}</p>
        </div>
    </div>
</div>
@endsection
