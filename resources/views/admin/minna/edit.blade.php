@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Sửa bài học Minna</h1>
        <a href="{{ route('admin.minna.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-center">
            ← Quay lại
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('admin.minna.update', $minna) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Số bài -->
            <div>
                <label for="number" class="block text-sm font-medium text-gray-700 mb-2">Số bài *</label>
                <input type="number" id="number" name="number" value="{{ old('number', $minna->number) }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('number') border-red-500 @enderror" 
                       placeholder="1" min="1" required>
                @error('number')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Tiêu đề -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề *</label>
                <input type="text" id="title" name="title" value="{{ old('title', $minna->title) }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('title') border-red-500 @enderror" 
                       placeholder="はじめまして" required>
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Mô tả -->
        <div class="mt-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Mô tả</label>
            <textarea id="description" name="description" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2
                             @error('description') border-red-500 @enderror"
                      placeholder="Mô tả bài học...">{{ old('description', $minna->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Buttons -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.minna.index') }}" 
               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                Hủy
            </a>
            <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Cập nhật bài học
            </button>
        </div>
    </form>
</div>
@endsection

