@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Báo lỗi nội dung</h1>
    <p class="text-gray-600 mt-2">Tiếp nhận lỗi từ người học về từ vựng, audio, quiz, bản dịch và nội dung bài học.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    @foreach($statuses as $status => $label)
        <div class="rounded-lg bg-white p-4 shadow-sm">
            <p class="text-xs text-gray-500">{{ $label }}</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats[$status] ?? 0 }}</p>
        </div>
    @endforeach
</div>

<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Trạng thái</label>
            <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                <option value="">Tất cả</option>
                @foreach($statuses as $status => $label)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Loại lỗi</label>
            <select name="category" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                <option value="">Tất cả</option>
                @foreach($categories as $category => $label)
                    <option value="{{ $category }}" @selected(request('category') === $category)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Tìm kiếm</label>
            <input name="q" value="{{ request('q') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Nội dung, đoạn bị lỗi...">
        </div>
        <div class="flex items-end gap-2">
            <button class="rounded-lg bg-gray-800 px-4 py-2 font-semibold text-white hover:bg-gray-900">Lọc</button>
            <a href="{{ route('admin.content-reports.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-300">Reset</a>
        </div>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full min-w-[920px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Báo lỗi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Người gửi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loại</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ngày gửi</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($reports as $report)
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $report->content_title ?: 'Trang học' }}</p>
                            <p class="mt-1 text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($report->description, 120) }}</p>
                            @if($report->selected_text)
                                <p class="mt-1 text-xs text-gray-500">Đoạn chọn: {{ \Illuminate\Support\Str::limit($report->selected_text, 80) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            <p class="font-semibold">{{ $report->user?->name }}</p>
                            <p class="text-xs text-gray-500">{{ $report->user?->email }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $report->categoryLabel() }}</td>
                        <td class="px-6 py-4">
                            <span class="rounded px-2 py-1 text-xs font-semibold {{ in_array($report->status, ['resolved', 'dismissed'], true) ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ $report->statusLabel() }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $report->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.content-reports.show', $report) }}" class="rounded bg-indigo-600 px-3 py-1 text-xs font-semibold text-white hover:bg-indigo-700">Xử lý</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-6 py-12 text-center text-gray-500">Chưa có báo lỗi nội dung.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reports->hasPages())
        <div class="border-t border-gray-200 px-6 py-4">{{ $reports->links() }}</div>
    @endif
</div>
@endsection
