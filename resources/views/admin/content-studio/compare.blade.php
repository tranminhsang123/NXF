@extends('adminlayout.app')

@section('content')
@php
    $formatValue = function ($value) {
        if (is_array($value)) {
            return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        }

        return $value === null || $value === '' ? '-' : (string) $value;
    };
@endphp

<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">So sánh version trước / sau</h1>
        <p class="text-gray-600 mt-2">Bài {{ $lesson->number }} - {{ $lesson->title }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.content-studio.index', ['q' => $lesson->number]) }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Quay lại Studio</a>
        <a href="{{ route('admin.content-ops.versions', ['type' => 'minna_lesson', 'id' => $lesson->id]) }}" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Lịch sử đầy đủ</a>
    </div>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-gray-500">Version dùng để so sánh</p>
        <p class="mt-2 font-bold text-gray-900">
            @if($compare['previousVersion'])
                #{{ $compare['previousVersion']->id }} • {{ $compare['previousVersion']->created_at?->format('d/m/Y H:i') }}
            @else
                Chưa có version trước
            @endif
        </p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-gray-500">Version mới nhất</p>
        <p class="mt-2 font-bold text-gray-900">
            @if($compare['latestVersion'])
                #{{ $compare['latestVersion']->id }} • {{ $compare['latestVersion']->action }}
            @else
                Chưa có lịch sử
            @endif
        </p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-gray-500">Trường thay đổi</p>
        <p class="mt-2 text-2xl font-bold text-red-600">{{ collect($compare['rows'])->where('changed', true)->count() }}</p>
    </div>
</div>

<div class="overflow-hidden rounded-lg bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Trường</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Version trước</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Hiện tại</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Trạng thái</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($compare['rows'] as $row)
                    <tr class="{{ $row['changed'] ? 'bg-amber-50/40' : '' }}">
                        <td class="px-6 py-4 align-top text-sm font-bold text-gray-900">{{ $row['key'] }}</td>
                        <td class="px-6 py-4 align-top text-xs text-gray-700">
                            <pre class="max-h-48 overflow-auto whitespace-pre-wrap rounded bg-gray-50 p-3">{{ $formatValue($row['before']) }}</pre>
                        </td>
                        <td class="px-6 py-4 align-top text-xs text-gray-700">
                            <pre class="max-h-48 overflow-auto whitespace-pre-wrap rounded bg-gray-50 p-3">{{ $formatValue($row['after']) }}</pre>
                        </td>
                        <td class="px-6 py-4 align-top">
                            @if($row['changed'])
                                <span class="rounded bg-amber-100 px-2 py-1 text-xs font-bold text-amber-800">Đã đổi</span>
                            @else
                                <span class="rounded bg-gray-100 px-2 py-1 text-xs font-bold text-gray-600">Không đổi</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">Chưa có dữ liệu để so sánh.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-6 rounded-lg bg-white p-6 shadow-sm">
    <h2 class="mb-4 text-lg font-bold text-gray-900">Các version gần đây</h2>
    <div class="space-y-3">
        @forelse($compare['versions'] as $version)
            <div class="flex flex-col gap-2 rounded-lg border border-gray-200 p-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="font-bold text-gray-900">#{{ $version->id }} • {{ $version->action }}</p>
                    <p class="text-sm text-gray-500">{{ $version->actor?->name ?? 'Hệ thống' }} • {{ $version->created_at?->format('d/m/Y H:i:s') }}</p>
                </div>
                @adminCan('content_ops.edit')
                    <form method="POST" action="{{ route('admin.content-ops.restore', $version) }}" onsubmit="return confirm('Khôi phục version này?')">
                        @csrf
                        <button class="rounded bg-amber-500 px-3 py-1.5 text-xs font-bold text-white hover:bg-amber-600">Khôi phục</button>
                    </form>
                @endadminCan
            </div>
        @empty
            <p class="rounded-lg bg-gray-50 p-4 text-sm text-gray-500">Chưa có version nào.</p>
        @endforelse
    </div>
</div>
@endsection
