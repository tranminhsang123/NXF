@extends('adminlayout.app')

@section('content')
@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg">{{ session('success') }}</div>
@endif
@if(session('info'))
    <div class="mb-4 p-4 bg-blue-100 border border-blue-300 text-blue-700 rounded-lg">{{ session('info') }}</div>
@endif

<div class="mb-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Chi tiết bài học: {{ $minna->title }}</h1>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.minna.edit', $minna) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Sửa
            </a>
            <a href="{{ route('admin.minna.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
                ← Quay lại
            </a>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Thông tin bài học</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-600">Số bài</p>
            <p class="text-lg font-semibold text-gray-900">Bài {{ str_pad($minna->number, 2, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Tiêu đề</p>
            <p class="text-lg font-semibold text-gray-900">{{ $minna->title }}</p>
        </div>
        @if($minna->description)
        <div class="md:col-span-2">
            <p class="text-sm text-gray-600">Mô tả</p>
            <p class="text-gray-900">{{ $minna->description }}</p>
        </div>
        @endif
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">Các phần của bài học ({{ $minna->sections->count() }})</h2>
    @if($minna->sections->count() > 0)
    <div class="space-y-4">
        @foreach($minna->sections as $section)
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <h3 class="font-semibold text-gray-900">{{ $section->title }}</h3>
                    <p class="text-sm text-gray-600 mt-1 break-all">Key: {{ $section->key }} | Thứ tự: {{ $section->order_index }}</p>
                </div>
                <a href="{{ route('admin.minna-section.edit', $section) }}"
                   class="inline-flex items-center justify-center w-full sm:w-auto px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700">
                    Sửa nội dung
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-8">
        <p class="text-gray-500 mb-4">Chưa có phần nào trong bài học này.</p>
        <form action="{{ route('admin.minna.add-sections', $minna) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700">
                + Tạo 5 phần mặc định
            </button>
        </form>
    </div>
    @endif
</div>
@endsection

