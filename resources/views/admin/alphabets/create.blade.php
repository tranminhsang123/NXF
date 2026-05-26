@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Thêm ký tự mới</h1>
        <a href="{{ route('admin.alphabets.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-center">
            ← Quay lại
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('admin.alphabets.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Ký tự -->
            <div>
                <label for="character" class="block text-sm font-medium text-gray-700 mb-2">Ký tự tiếng Nhật *</label>
                <input type="text" id="character" name="character" value="{{ old('character') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-2xl font-bold
                              @error('character') border-red-500 @enderror" 
                       placeholder="あ" required>
                @error('character')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Romaji -->
            <div>
                <label for="romaji" class="block text-sm font-medium text-gray-700 mb-2">Romaji *</label>
                <input type="text" id="romaji" name="romaji" value="{{ old('romaji') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('romaji') border-red-500 @enderror" 
                       placeholder="a" required>
                @error('romaji')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Loại -->
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Loại bảng chữ *</label>
                <select id="type" name="type" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2
                               @error('type') border-red-500 @enderror" required>
                    <option value="">Chọn loại</option>
                    <option value="hiragana" {{ old('type') == 'hiragana' ? 'selected' : '' }}>Hiragana</option>
                    <option value="katakana" {{ old('type') == 'katakana' ? 'selected' : '' }}>Katakana</option>
                    <option value="romaji" {{ old('type') == 'romaji' ? 'selected' : '' }}>Romaji</option>
                </select>
                @error('type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Category -->
            <div>
                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Phân loại</label>
                <select id="category" name="category" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2
                               @error('category') border-red-500 @enderror">
                    <option value="">Không có</option>
                    <option value="seion" {{ old('category') == 'seion' ? 'selected' : '' }}>Seion (清音)</option>
                    <option value="dakuon" {{ old('category') == 'dakuon' ? 'selected' : '' }}>Dakuon (濁音)</option>
                    <option value="yoon" {{ old('category') == 'yoon' ? 'selected' : '' }}>Yōon (拗音)</option>
                </select>
                @error('category')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Buttons -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.alphabets.index') }}" 
               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                Hủy
            </a>
            <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Thêm ký tự
            </button>
        </div>
    </form>
</div>
@endsection
