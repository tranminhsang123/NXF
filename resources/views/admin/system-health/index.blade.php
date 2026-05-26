@extends('adminlayout.app')

@section('content')
@php
    $statusStyles = [
        'ok' => 'bg-green-50 text-green-800 border-green-200',
        'warning' => 'bg-amber-50 text-amber-800 border-amber-200',
        'error' => 'bg-red-50 text-red-800 border-red-200',
    ];
    $statusLabels = [
        'ok' => 'Ổn',
        'warning' => 'Cần chú ý',
        'error' => 'Lỗi',
    ];
@endphp

<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Sức khỏe hệ thống</h1>
        <p class="text-gray-600 mt-2">Kiểm tra nhanh các thành phần vận hành cho nội dung, audio, email và kiểm duyệt.</p>
    </div>
    <a href="{{ route('admin.dashboard') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Quay lại dashboard</a>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    <div class="rounded-lg bg-white p-4 shadow-sm">
        <p class="text-xs text-gray-500">Yêu cầu duyệt</p>
        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['pending_publish_requests'] }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow-sm">
        <p class="text-xs text-gray-500">Report chat</p>
        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['pending_chat_reports'] }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow-sm">
        <p class="text-xs text-gray-500">Báo lỗi nội dung</p>
        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['pending_content_reports'] }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow-sm">
        <p class="text-xs text-gray-500">Campaign nháp</p>
        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['draft_campaigns'] }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow-sm">
        <p class="text-xs text-gray-500">Job đang chờ</p>
        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['queued_jobs'] }}</p>
    </div>
    <div class="rounded-lg bg-white p-4 shadow-sm">
        <p class="text-xs text-gray-500">Job lỗi</p>
        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['failed_jobs'] }}</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach($checks as $check)
            <div class="rounded-lg bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">{{ $check['name'] }}</h2>
                        <p class="mt-2 text-sm text-gray-600">{{ $check['summary'] }}</p>
                    </div>
                    <span class="shrink-0 rounded border px-2 py-1 text-xs font-semibold {{ $statusStyles[$check['status']] ?? 'bg-gray-50 text-gray-700 border-gray-200' }}">
                        {{ $statusLabels[$check['status']] ?? $check['status'] }}
                    </span>
                </div>

                @if(! empty($check['meta']))
                    <dl class="mt-4 divide-y divide-gray-100 text-sm">
                        @foreach($check['meta'] as $key => $value)
                            <div class="flex items-center justify-between gap-4 py-2">
                                <dt class="text-gray-500">{{ $key }}</dt>
                                <dd class="text-right font-semibold text-gray-800 break-all">{{ $value }}</dd>
                            </div>
                        @endforeach
                    </dl>
                @endif
            </div>
        @endforeach
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900">Lệnh vận hành</h2>
        <div class="mt-4 space-y-3">
            @foreach($commands as $command)
                <div class="rounded-lg bg-gray-900 px-3 py-2 font-mono text-xs text-gray-100">{{ $command }}</div>
            @endforeach
        </div>
    </div>
</div>
@endsection
