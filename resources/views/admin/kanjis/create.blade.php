@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Thêm Kanji mới</h1>
        <a href="{{ route('admin.kanjis.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-center">
            ← Quay lại
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('admin.kanjis.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Ký tự -->
            <div>
                <label for="character" class="block text-sm font-medium text-gray-700 mb-2">Ký tự Kanji *</label>
                <input type="text" id="character" name="character" value="{{ old('character') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-3xl font-bold text-center
                              @error('character') border-red-500 @enderror" 
                       placeholder="日" required>
                @error('character')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Nghĩa -->
            <div>
                <label for="meaning" class="block text-sm font-medium text-gray-700 mb-2">Nghĩa *</label>
                <input type="text" id="meaning" name="meaning" value="{{ old('meaning') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('meaning') border-red-500 @enderror" 
                       placeholder="ngày, mặt trời" required>
                @error('meaning')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Âm On -->
            <div>
                <label for="on_reading" class="block text-sm font-medium text-gray-700 mb-2">Âm On</label>
                <input type="text" id="on_reading" name="on_reading" value="{{ old('on_reading') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('on_reading') border-red-500 @enderror" 
                       placeholder="ニチ, ジツ">
                @error('on_reading')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Âm Kun -->
            <div>
                <label for="kun_reading" class="block text-sm font-medium text-gray-700 mb-2">Âm Kun</label>
                <input type="text" id="kun_reading" name="kun_reading" value="{{ old('kun_reading') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('kun_reading') border-red-500 @enderror" 
                       placeholder="ひ, び, か">
                @error('kun_reading')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Cấp độ -->
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Cấp độ JLPT *</label>
                <select id="level" name="level" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2
                               @error('level') border-red-500 @enderror" required>
                    <option value="">Chọn cấp độ</option>
                    <option value="N5" {{ old('level') == 'N5' ? 'selected' : '' }}>N5</option>
                    <option value="N4" {{ old('level') == 'N4' ? 'selected' : '' }}>N4</option>
                    <option value="N3" {{ old('level') == 'N3' ? 'selected' : '' }}>N3</option>
                    <option value="N2" {{ old('level') == 'N2' ? 'selected' : '' }}>N2</option>
                    <option value="N1" {{ old('level') == 'N1' ? 'selected' : '' }}>N1</option>
                </select>
                @error('level')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Số nét -->
            <div>
                <label for="stroke_count" class="block text-sm font-medium text-gray-700 mb-2">Số nét *</label>
                <input type="number" id="stroke_count" name="stroke_count" value="{{ old('stroke_count') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('stroke_count') border-red-500 @enderror" 
                       placeholder="4" min="1" max="30" required>
                @error('stroke_count')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Bộ thủ -->
            <div>
                <label for="radical" class="block text-sm font-medium text-gray-700 mb-2">Bộ thủ</label>
                <input type="text" id="radical" name="radical" value="{{ old('radical') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('radical') border-red-500 @enderror" 
                       placeholder="日">
                @error('radical')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Ví dụ -->
        <div class="mt-6">
            <label for="examples" class="block text-sm font-medium text-gray-700 mb-2">Ví dụ sử dụng</label>
            <textarea id="examples" name="examples" rows="3"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2
                             @error('examples') border-red-500 @enderror"
                      placeholder="今日 (きょう) - hôm nay">{{ old('examples') }}</textarea>
            @error('examples')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Buttons -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.kanjis.index') }}" 
               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                Hủy
            </a>
            <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Thêm Kanji
            </button>
        </div>
    </form>
</div>
@endsection

