@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Chi tiết Kanji</h1>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.kanjis.edit', $kanji) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700">
                Sửa
            </a>
            <a href="{{ route('admin.kanjis.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Quay lại
            </a>
        </div>
    </div>
</div>

<!-- Kanji Character Card -->
<div class="bg-white rounded-lg shadow-sm p-4 md:p-8 mb-6 text-center">
    <div class="text-6xl md:text-9xl font-bold text-gray-900 mb-4 japanese-text">{{ $kanji->character }}</div>
    <div class="text-xl md:text-2xl font-semibold text-gray-700 mb-2 break-words">{{ $kanji->meaning }}</div>
    <div class="flex flex-col sm:flex-row justify-center gap-2 sm:gap-4 text-sm text-gray-600">
        @if($kanji->on_reading)
            <span>On: <span class="font-semibold">{{ $kanji->on_reading }}</span></span>
        @endif
        @if($kanji->kun_reading)
            <span>Kun: <span class="font-semibold">{{ $kanji->kun_reading }}</span></span>
        @endif
    </div>
</div>

<!-- Details Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
    <!-- Basic Info -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Thông tin cơ bản</h2>
        <dl class="space-y-3">
            <div>
                <dt class="text-sm font-medium text-gray-500">Cấp độ JLPT</dt>
                <dd class="mt-1">
                    <span class="px-3 py-1 text-sm font-semibold rounded-full
                        {{ $kanji->level == 'N5' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $kanji->level == 'N4' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $kanji->level == 'N3' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $kanji->level == 'N2' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $kanji->level == 'N1' ? 'bg-purple-100 text-purple-800' : '' }}">
                        {{ $kanji->level }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Số nét viết</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $kanji->stroke_count }} nét</dd>
            </div>
            @if($kanji->radical)
            <div>
                <dt class="text-sm font-medium text-gray-500">Bộ thủ</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $kanji->radical }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <!-- Readings -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-4">Cách đọc</h2>
        <dl class="space-y-3">
            <div>
                <dt class="text-sm font-medium text-gray-500">Âm On (音読み)</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $kanji->on_reading ? $kanji->on_reading : '-' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">Âm Kun (訓読み)</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $kanji->kun_reading ? $kanji->kun_reading : '-' }}
                </dd>
            </div>
        </dl>
    </div>
</div>

<!-- Examples -->
@if($kanji->examples)
<div class="bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Ví dụ sử dụng</h2>
    <div class="text-sm text-gray-700 whitespace-pre-wrap">{{ $kanji->examples }}</div>
</div>
@endif

<style>
    .japanese-text {
        font-family: 'Hiragino Sans', 'Yu Gothic', 'Meiryo', sans-serif;
    }
</style>
@endsection

