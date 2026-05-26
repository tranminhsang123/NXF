@extends('adminlayout.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Xem trước: {{ $title }}</h1>
        <p class="text-gray-600 mt-2">{{ $label }} #{{ $item->id }} - {{ $item->publishStatusLabel() }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.content-ops.versions', ['type' => $type, 'id' => $item->id]) }}" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Phiên bản</a>
        <a href="{{ route('admin.content-ops.index', ['type' => $type]) }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Quay lại</a>
    </div>
</div>

@if(session('success'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Dữ liệu nội dung</h2>
        <dl class="divide-y divide-gray-100">
            @foreach($snapshot as $key => $value)
                <div class="grid grid-cols-1 md:grid-cols-4 gap-3 py-3">
                    <dt class="text-sm font-semibold text-gray-600">{{ $key }}</dt>
                    <dd class="md:col-span-3 text-sm text-gray-900 break-words">
                        @if(is_array($value))
                            <pre class="max-h-96 overflow-auto rounded bg-gray-50 p-3 text-xs">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @else
                            {{ $value ?? '-' }}
                        @endif
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>

    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Kiểm tra nội dung</h2>
            @if(empty($issues))
                <p class="rounded-lg bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">Chưa phát hiện lỗi.</p>
            @else
                <ul class="space-y-2 text-sm text-red-700">
                    @foreach($issues as $issue)
                        <li class="rounded-lg bg-red-50 px-3 py-2">{{ $issue }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            @php
                $qaStyles = [
                    'pass' => 'bg-green-50 text-green-800 border-green-200',
                    'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
                    'fail' => 'bg-red-50 text-red-800 border-red-200',
                ];
                $qaLabels = [
                    'pass' => 'Đạt',
                    'warning' => 'Cần xem',
                    'fail' => 'Chưa đạt',
                ];
            @endphp
            <div class="mb-4 flex items-start justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Checklist QA trước publish</h2>
                    <p class="mt-1 text-xs text-gray-500">Các mục bắt buộc phải đạt trước khi xuất bản.</p>
                </div>
                <span class="rounded border px-2 py-1 text-xs font-semibold {{ $qualityChecklist['passed'] ? 'border-green-200 bg-green-50 text-green-800' : 'border-red-200 bg-red-50 text-red-800' }}">
                    {{ $qualityChecklist['passed'] ? 'Sẵn sàng' : ($qualityChecklist['blocking_count'].' mục chặn') }}
                </span>
            </div>

            <div class="space-y-3">
                @foreach($qualityChecklist['items'] as $qaItem)
                    <div class="rounded-lg border p-3 {{ $qaStyles[$qaItem['status']] ?? 'bg-gray-50 text-gray-800 border-gray-200' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold">{{ $qaItem['label'] }}</p>
                                <p class="mt-1 text-xs">{{ $qaItem['summary'] }}</p>
                            </div>
                            <span class="shrink-0 rounded bg-white/70 px-2 py-0.5 text-[11px] font-bold">{{ $qaLabels[$qaItem['status']] ?? $qaItem['status'] }}</span>
                        </div>
                        @if(!empty($qaItem['details']))
                            <ul class="mt-2 list-disc space-y-1 pl-4 text-xs">
                                @foreach($qaItem['details'] as $detail)
                                    <li>{{ $detail }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="mt-4 flex flex-wrap gap-2">
                <a href="{{ route('admin.audio.index') }}" class="rounded bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200">Mở Audio/TTS</a>
                <a href="{{ route('admin.content-reports.index') }}" class="rounded bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200">Mở báo lỗi</a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Trạng thái xuất bản</h2>
            <form method="POST" action="{{ route('admin.content-ops.status', ['type' => $type, 'id' => $item->id]) }}" class="space-y-3">
                @csrf
                @method('PATCH')
                <select name="publish_status" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    @foreach(\App\Support\PublishStatus::labels() as $status => $label)
                        <option value="{{ $status }}" @selected(($item->publish_status ?? 'published') === $status)>{{ $label }}</option>
                    @endforeach
                </select>
                @adminCan('content_ops.edit')
                    <button class="w-full rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">Cập nhật trạng thái</button>
                @endadminCan
            </form>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Duyệt và hẹn lịch</h2>

            @adminCan('content_ops.edit')
                <form method="POST" action="{{ route('admin.content-ops.publish-requests.store', ['type' => $type, 'id' => $item->id]) }}" class="space-y-3">
                    @csrf
                    <select name="requested_status" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                        <option value="published">Yêu cầu xuất bản</option>
                        <option value="archived">Yêu cầu lưu trữ</option>
                    </select>
                    <input type="datetime-local" name="scheduled_publish_at" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    <textarea name="notes" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Ghi chú cho người duyệt">{{ old('notes') }}</textarea>
                    <button class="w-full rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Gửi yêu cầu duyệt</button>
                </form>
            @endadminCan

            <div class="mt-5 space-y-3">
                @forelse($publishRequests as $publishRequest)
                    <div class="rounded-lg border border-gray-100 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="font-semibold text-gray-900">
                                    {{ \App\Support\PublishStatus::labels()[$publishRequest->requested_status] ?? $publishRequest->requested_status }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $publishRequest->requester?->name ?? 'Admin' }} - {{ $publishRequest->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <span class="rounded bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-700">{{ $publishRequest->statusLabel() }}</span>
                        </div>

                        @if($publishRequest->scheduled_publish_at)
                            <p class="mt-2 text-xs text-blue-700">Lịch dự kiến: {{ $publishRequest->scheduled_publish_at->format('d/m/Y H:i') }}</p>
                        @endif
                        @if($publishRequest->notes)
                            <p class="mt-2 text-sm text-gray-700">{{ $publishRequest->notes }}</p>
                        @endif
                        @if($publishRequest->review_notes)
                            <p class="mt-2 rounded bg-gray-50 px-3 py-2 text-xs text-gray-600">Phản hồi: {{ $publishRequest->review_notes }}</p>
                        @endif

                        @if($publishRequest->status === \App\Models\ContentPublishRequest::STATUS_PENDING)
                            @adminCan('content_ops.edit')
                                <div class="mt-3 space-y-2">
                                    <form method="POST" action="{{ route('admin.content-ops.publish-requests.approve', $publishRequest) }}" class="space-y-2">
                                        @csrf
                                        <input type="datetime-local" name="scheduled_publish_at" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" value="{{ $publishRequest->scheduled_publish_at?->format('Y-m-d\TH:i') }}">
                                        <textarea name="review_notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Ghi chú duyệt"></textarea>
                                        <button class="w-full rounded bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700">Duyệt / áp dụng</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.content-ops.publish-requests.reject', $publishRequest) }}" class="space-y-2">
                                        @csrf
                                        <textarea name="review_notes" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm" placeholder="Lý do từ chối"></textarea>
                                        <button class="w-full rounded bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Từ chối</button>
                                    </form>
                                </div>
                            @endadminCan
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-gray-500">Chưa có yêu cầu duyệt.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
