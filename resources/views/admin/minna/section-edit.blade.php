@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <div class="flex items-center justify-between flex-wrap gap-3">
        <div>
            <nav class="text-sm text-gray-600 mb-2">
                <a href="{{ route('admin.minna.index') }}" class="text-blue-600 hover:underline">Minna</a>
                <span class="mx-1">/</span>
                <a href="{{ route('admin.minna.show', $minnaSection->lesson) }}" class="text-blue-600 hover:underline">Bài {{ str_pad($minnaSection->lesson->number, 2, '0', STR_PAD_LEFT) }}</a>
                <span class="mx-1">/</span>
                <span class="text-gray-900 font-medium">Sửa: {{ $minnaSection->title }}</span>
            </nav>
            <h1 class="text-2xl font-bold text-gray-900">Chỉnh sửa nội dung: {{ $minnaSection->title }}</h1>
            <p class="text-sm text-gray-500 mt-1 break-all">Key: {{ $minnaSection->key }} | Thứ tự: {{ $minnaSection->order_index }}</p>
        </div>
        <a href="{{ route('admin.minna.show', $minnaSection->lesson) }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 w-full sm:w-auto text-center">
            ← Quay lại bài học
        </a>
    </div>
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg">
        {{ session('success') }}
    </div>
@endif

@php
    $content = $minnaSection->content ?? [];
    $key = $minnaSection->key;
@endphp

<div class="bg-white rounded-lg shadow-sm p-6">
    <form id="section-form" action="{{ route('admin.minna-section.update', $minnaSection) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề phần</label>
                <input type="text" id="title" name="title" value="{{ old('title', $minnaSection->title) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('title') border-red-500 @enderror"
                       placeholder="vd: Phần 1: Từ vựng">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="media_url" class="block text-sm font-medium text-gray-700 mb-2">Media URL</label>
                <input type="url" id="media_url" name="media_url" value="{{ old('media_url', $minnaSection->media_url) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 @error('media_url') border-red-500 @enderror"
                       placeholder="https://...">
                @error('media_url')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Form editor theo loại section --}}
        @if($key === 'tu-vung')
            @include('admin.minna.editors.tu-vung', ['content' => $content])
        @elseif($key === 'ngu-phap')
            @include('admin.minna.editors.ngu-phap', ['content' => $content])
        @elseif($key === 'luyen-doc')
            @include('admin.minna.editors.luyen-doc', ['content' => $content])
        @elseif($key === 'hoi-thoai')
            @include('admin.minna.editors.hoi-thoai', ['content' => $content])
        @elseif($key === 'han-tu')
            @include('admin.minna.editors.han-tu', ['content' => $content])
        @else
            {{-- Loại khác (nếu có): dùng JSON --}}
            @include('admin.minna.editors.json-fallback', ['content' => $content, 'key' => $key])
        @endif

        <div class="mt-8 flex flex-col-reverse sm:flex-row justify-end gap-3">
            <a href="{{ route('admin.minna.show', $minnaSection->lesson) }}"
               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 text-center">
                Hủy
            </a>
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 w-full sm:w-auto">
                Lưu nội dung
            </button>
        </div>
    </form>
</div>
@endsection
