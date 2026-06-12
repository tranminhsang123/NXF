@extends('adminlayout.app')

@section('admin_title', 'Lịch sử phiên bản')

@section('content')
@php
    $actionLabels = [
        'created' => 'Tạo mới',
        'updated' => 'Cập nhật',
        'deleted' => 'Xóa',
        'restored' => 'Khôi phục',
    ];

    $fieldLabels = [
        'number' => 'Số bài',
        'title' => 'Tiêu đề',
        'description' => 'Mô tả',
        'content' => 'Nội dung học',
        'media_url' => 'Media',
        'publish_status' => 'Trạng thái xuất bản',
        'published_at' => 'Ngày xuất bản',
        'archived_at' => 'Ngày lưu trữ',
        'created_at' => 'Ngày tạo',
        'updated_at' => 'Cập nhật lúc',
        'order_index' => 'Thứ tự',
        'key' => 'Loại phần học',
        'level' => 'Cấp độ',
        'meaning' => 'Nghĩa',
        'onyomi' => 'Âm On',
        'kunyomi' => 'Âm Kun',
        'romaji' => 'Romaji',
    ];

    $statusLabels = [
        'draft' => 'Bản nháp',
        'published' => 'Đã xuất bản',
        'archived' => 'Đã lưu trữ',
    ];

    $formatValue = function ($value, string $field = '') use ($statusLabels) {
        if ($value === null || $value === '') {
            return ['kind' => 'empty', 'text' => 'Trống', 'detail' => null];
        }

        if (is_bool($value)) {
            return ['kind' => 'text', 'text' => $value ? 'Có' : 'Không', 'detail' => null];
        }

        if ($field === 'publish_status') {
            return ['kind' => 'text', 'text' => $statusLabels[$value] ?? (string) $value, 'detail' => null];
        }

        if (str_ends_with($field, '_at') && is_string($value)) {
            try {
                return ['kind' => 'text', 'text' => \Carbon\Carbon::parse($value)->timezone(config('app.timezone'))->format('d/m/Y H:i'), 'detail' => null];
            } catch (\Throwable) {
                return ['kind' => 'text', 'text' => $value, 'detail' => null];
            }
        }

        if (is_array($value)) {
            $count = count($value);
            return [
                'kind' => 'structured',
                'text' => $count > 0 ? 'Dữ liệu cấu trúc đã thay đổi ('.$count.' mục)' : 'Dữ liệu cấu trúc rỗng',
                'detail' => json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            ];
        }

        $text = (string) $value;
        if (mb_strlen($text) > 220) {
            return [
                'kind' => 'long',
                'text' => mb_substr($text, 0, 220).'...',
                'detail' => $text,
            ];
        }

        return ['kind' => 'text', 'text' => $text, 'detail' => null];
    };

    $changeRows = function (?array $changes) use ($fieldLabels, $formatValue) {
        if (empty($changes)) {
            return collect();
        }

        return collect($changes)->map(function ($change, $field) use ($fieldLabels, $formatValue) {
            $before = is_array($change) && array_key_exists('before', $change) ? $change['before'] : null;
            $after = is_array($change) && array_key_exists('after', $change) ? $change['after'] : null;

            return [
                'field' => (string) $field,
                'label' => $fieldLabels[$field] ?? \Illuminate\Support\Str::headline((string) $field),
                'before' => $formatValue($before, (string) $field),
                'after' => $formatValue($after, (string) $field),
            ];
        })->values();
    };
@endphp

<div class="space-y-5 md:space-y-7">
    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-end">
        <div>
            <div class="flex items-center gap-2 text-sm font-semibold text-slate-500">
                <a href="{{ route('admin.content-ops.index') }}" class="text-red-600 hover:text-red-700">Vận hành nội dung</a>
                <span>/</span>
                <span>Phiên bản</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950 md:text-3xl">Lịch sử phiên bản</h1>
            <p class="mt-1 max-w-3xl text-sm text-slate-500 md:text-base">{{ $title }}</p>
        </div>
        <a href="{{ route('admin.content-ops.preview', ['type' => $type, 'id' => $item->id]) }}" class="inline-flex h-10 w-fit items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
            Quay lại xem trước
        </a>
    </div>

    @if(session('success'))
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('success') }}</div>
    @endif

    <div class="space-y-4">
        @forelse($versions as $version)
            @php
                $rows = $changeRows($version->changes);
            @endphp
            <section class="admin-card overflow-hidden rounded-lg">
                <div class="flex flex-col gap-3 border-b border-slate-200 bg-white px-4 py-4 md:flex-row md:items-center md:justify-between md:px-5">
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700">
                                {{ $actionLabels[$version->action] ?? $version->action }}
                            </span>
                            <span class="text-sm font-semibold text-slate-950">{{ $version->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        <p class="mt-1 text-sm text-slate-500">
                            Người thao tác: <span class="font-semibold text-slate-700">{{ $version->actor?->name ?? 'Hệ thống' }}</span>
                        </p>
                    </div>

                    @adminCan('content_ops.edit')
                        <form method="POST" action="{{ route('admin.content-ops.restore', $version) }}" onsubmit="return confirm('Khôi phục phiên bản này?')">
                            @csrf
                            <button class="inline-flex h-9 items-center justify-center rounded-lg bg-amber-500 px-3 text-xs font-bold text-white shadow-sm hover:bg-amber-600">
                                Khôi phục
                            </button>
                        </form>
                    @endadminCan
                </div>

                <div class="p-4 md:p-5">
                    @if($rows->isEmpty())
                        <div class="rounded-lg border border-dashed border-slate-200 bg-slate-50 px-4 py-6 text-center text-sm text-slate-500">
                            Phiên bản này không ghi nhận thay đổi chi tiết.
                        </div>
                    @else
                        <div class="overflow-hidden rounded-lg border border-slate-200">
                            <div class="hidden grid-cols-[13rem_minmax(0,1fr)_minmax(0,1fr)] gap-0 bg-slate-50 text-xs font-bold uppercase tracking-wide text-slate-500 md:grid">
                                <div class="px-4 py-3">Trường</div>
                                <div class="border-l border-slate-200 px-4 py-3">Trước</div>
                                <div class="border-l border-slate-200 px-4 py-3">Sau</div>
                            </div>

                            <div class="divide-y divide-slate-200">
                                @foreach($rows as $row)
                                    <div class="grid grid-cols-1 gap-0 md:grid-cols-[13rem_minmax(0,1fr)_minmax(0,1fr)]">
                                        <div class="bg-slate-50 px-4 py-3 text-sm font-bold text-slate-700 md:bg-white">
                                            {{ $row['label'] }}
                                        </div>
                                        @foreach(['before' => 'Trước', 'after' => 'Sau'] as $side => $sideLabel)
                                            @php $value = $row[$side]; @endphp
                                            <div class="border-t border-slate-100 px-4 py-3 text-sm text-slate-700 md:border-l md:border-t-0">
                                                <p class="mb-1 text-xs font-bold uppercase tracking-wide text-slate-400 md:hidden">{{ $sideLabel }}</p>
                                                @if($value['kind'] === 'empty')
                                                    <span class="text-slate-400">{{ $value['text'] }}</span>
                                                @elseif($value['kind'] === 'structured')
                                                    <div class="space-y-2">
                                                        <p class="font-semibold text-slate-700">{{ $value['text'] }}</p>
                                                        <details class="rounded-lg border border-slate-200 bg-slate-50">
                                                            <summary class="cursor-pointer px-3 py-2 text-xs font-bold text-slate-600">Xem chi tiết kỹ thuật</summary>
                                                            <pre class="max-h-64 overflow-auto border-t border-slate-200 p-3 text-xs leading-5 text-slate-700">{{ $value['detail'] }}</pre>
                                                        </details>
                                                    </div>
                                                @elseif($value['kind'] === 'long')
                                                    <div class="space-y-2">
                                                        <p class="leading-6">{{ $value['text'] }}</p>
                                                        <details class="rounded-lg border border-slate-200 bg-slate-50">
                                                            <summary class="cursor-pointer px-3 py-2 text-xs font-bold text-slate-600">Xem đầy đủ</summary>
                                                            <div class="whitespace-pre-wrap border-t border-slate-200 p-3 text-xs leading-5 text-slate-700">{{ $value['detail'] }}</div>
                                                        </details>
                                                    </div>
                                                @else
                                                    <span class="whitespace-pre-wrap leading-6">{{ $value['text'] }}</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @empty
            <div class="admin-card rounded-lg px-6 py-12 text-center text-slate-500">Chưa có phiên bản.</div>
        @endforelse
    </div>

    @if($versions->hasPages())
        <div class="admin-card rounded-lg px-4 py-3">{{ $versions->links() }}</div>
    @endif
</div>
@endsection
