@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Thêm dữ liệu khóa học mới</h1>
        <a href="{{ route('admin.course-data.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 text-center">
            ← Quay lại
        </a>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="{{ route('admin.course-data.store') }}" method="POST">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Section Type -->
            <div>
                <label for="section_type" class="block text-sm font-medium text-gray-700 mb-2">Loại section *</label>
                <select id="section_type" name="section_type" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2
                               @error('section_type') border-red-500 @enderror" required>
                    <option value="">Chọn loại</option>
                    <option value="speed_master_n5" {{ old('section_type') == 'speed_master_n5' ? 'selected' : '' }}>Speed Master N5</option>
                    <option value="luyen_doc" {{ old('section_type') == 'luyen_doc' ? 'selected' : '' }}>Luyện đọc</option>
                    <option value="marugoto_n5" {{ old('section_type') == 'marugoto_n5' ? 'selected' : '' }}>Marugoto N5</option>
                    <option value="korede_daijoubu" {{ old('section_type') == 'korede_daijoubu' ? 'selected' : '' }}>Korede Daijoubu</option>
                    <option value="gokaku_dekiru" {{ old('section_type') == 'gokaku_dekiru' ? 'selected' : '' }}>Gokaku Dekiru</option>
                    <option value="tanki_master_n5" {{ old('section_type') == 'tanki_master_n5' ? 'selected' : '' }}>Tanki Master N5</option>
                </select>
                @error('section_type')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Section Key -->
            <div>
                <label for="section_key" class="block text-sm font-medium text-gray-700 mb-2">Section Key</label>
                <select id="section_key" name="section_key" 
                        class="w-full border border-gray-300 rounded-lg px-3 py-2
                               @error('section_key') border-red-500 @enderror">
                    <option value="">Không có</option>
                    <option value="tuVung" {{ old('section_key') == 'tuVung' ? 'selected' : '' }}>Từ vựng</option>
                    <option value="nguPhap" {{ old('section_key') == 'nguPhap' ? 'selected' : '' }}>Ngữ pháp</option>
                    <option value="docHieu" {{ old('section_key') == 'docHieu' ? 'selected' : '' }}>Đọc hiểu</option>
                    <option value="ngheHieu" {{ old('section_key') == 'ngheHieu' ? 'selected' : '' }}>Nghe hiểu</option>
                </select>
                @error('section_key')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Bài -->
            <div>
                <label for="bai" class="block text-sm font-medium text-gray-700 mb-2">Bài</label>
                <input type="text" id="bai" name="bai" value="{{ old('bai') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('bai') border-red-500 @enderror" 
                       placeholder="Bài 1">
                @error('bai')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Tiêu đề -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Tiêu đề</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('title') border-red-500 @enderror" 
                       placeholder="Tiêu đề...">
                @error('title')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Thứ tự -->
            <div>
                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">Thứ tự *</label>
                <input type="number" id="order" name="order" value="{{ old('order', 1) }}" 
                       class="w-full border border-gray-300 rounded-lg px-3 py-2
                              @error('order') border-red-500 @enderror" 
                       placeholder="1" min="0" required>
                @error('order')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <!-- Content: Form trực quan theo loại đã chọn -->
        <div class="mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Nội dung</label>
            <p class="text-xs text-gray-500 mb-2">Chọn "Loại section" và "Section Key" ở trên → form nhập sẽ hiện tương ứng.</p>
            
            @php 
                $initContent = old('content'); 
                $initContent = is_array($initContent) ? $initContent : [];
            @endphp

            <div id="editor-words" class="content-editor hidden">
                @include('admin.course-data.editors.words', ['content' => $initContent])
            </div>
            <div id="editor-luyen-doc" class="content-editor hidden">
                @include('admin.course-data.editors.luyen-doc', ['content' => $initContent])
            </div>
            <div id="editor-ngu-phap" class="content-editor hidden">
                @include('admin.course-data.editors.ngu-phap', ['content' => $initContent])
            </div>
            <div id="editor-doc-hieu" class="content-editor hidden">
                @include('admin.course-data.editors.doc-hieu', ['content' => $initContent])
            </div>
            <div id="editor-marugoto" class="content-editor hidden">
                @include('admin.course-data.editors.marugoto', ['content' => $initContent])
            </div>
            <div id="editor-no" class="content-editor hidden">
                @include('admin.course-data.editors.no-editor', ['jsonSeed' => old('content_json', '')])
            </div>
            
            @error('content')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <!-- Buttons -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('admin.course-data.index') }}" 
               class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                Hủy
            </a>
            <button type="submit" 
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                Thêm dữ liệu
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sectionType = document.getElementById('section_type');
    const sectionKey = document.getElementById('section_key');
    const editors = {
        words: document.getElementById('editor-words'),
        luyenDoc: document.getElementById('editor-luyen-doc'),
        nguPhap: document.getElementById('editor-ngu-phap'),
        docHieu: document.getElementById('editor-doc-hieu'),
        marugoto: document.getElementById('editor-marugoto'),
        no: document.getElementById('editor-no')
    };
    function setEditorDisabled(editor, disabled) {
        editor?.querySelectorAll('input, textarea, select').forEach(inp => inp.disabled = disabled);
    }
    function updateEditor() {
        const st = sectionType?.value || '';
        const sk = sectionKey?.value || '';
        Object.entries(editors).forEach(([k, el]) => {
            if (el) { el.classList.add('hidden'); setEditorDisabled(el, true); }
        });
        let active = null;
        if (sk === 'tuVung' && st !== 'marugoto_n5') active = editors.words;
        else if (st === 'luyen_doc') active = editors.luyenDoc;
        else if (sk === 'nguPhap' && st !== 'marugoto_n5') active = editors.nguPhap;
        else if (['docHieu','ngheHieu'].includes(sk)) active = editors.docHieu;
        else if (st === 'marugoto_n5') active = editors.marugoto;
        else active = editors.no;
        if (active) { active.classList.remove('hidden'); setEditorDisabled(active, false); }
    }
    sectionType?.addEventListener('change', updateEditor);
    sectionKey?.addEventListener('change', updateEditor);
    updateEditor();
});
</script>
@endsection

