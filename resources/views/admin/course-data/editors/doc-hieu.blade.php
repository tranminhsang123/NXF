{{-- Editor cho docHieu: {passage, question, options: [{text, romaji?, meaning?}], correct_answer, explanation} --}}
@php
    $passage = $content['passage'] ?? '';
    $question = $content['question'] ?? '';
    $options = $content['options'] ?? [];
    $correctAnswer = $content['correct_answer'] ?? 0;
    $explanation = $content['explanation'] ?? '';
@endphp
<div class="mt-6 space-y-4">
    <div>
        <label class="block font-medium text-gray-900 mb-1">Đoạn văn</label>
        <textarea name="content[passage]" rows="5" class="w-full border rounded px-3 py-2" placeholder="Nhập đoạn văn tiếng Nhật...">{{ $passage }}</textarea>
    </div>
    <div>
        <label class="block font-medium text-gray-900 mb-1">Câu hỏi</label>
        <input type="text" name="content[question]" value="{{ $question }}" class="w-full border rounded px-3 py-2" placeholder="Nội dung câu hỏi">
    </div>
    <div>
        <div class="flex items-center justify-between mb-1">
            <label class="block font-medium text-gray-900">Đáp án (mỗi dòng 1 đáp án: text hoặc text|romaji|nghĩa)</label>
            <button type="button" onclick="addDocHieuOption()" class="text-sm bg-green-600 text-white px-2 py-1 rounded">+ Thêm</button>
        </div>
        <div id="docheiu-options" class="space-y-2">
            @foreach($options as $oi => $opt)
            <div class="flex gap-2 option-row">
                <span class="text-gray-500 mt-2">{{ $oi + 1 }}.</span>
                <input type="text" name="content[options][{{ $oi }}]" value="{{ ($opt['text'] ?? '') }}{{ !empty($opt['romaji']) ? '|' . $opt['romaji'] : '' }}{{ !empty($opt['meaning']) ? '|' . $opt['meaning'] : '' }}" class="flex-1 border rounded px-2 py-1" placeholder="Đáp án">
                <button type="button" onclick="removeDocHieuOption(this)" class="text-red-600 px-2">✕</button>
            </div>
            @endforeach
        </div>
    </div>
    <div>
        <label class="block font-medium text-gray-900 mb-1">Đáp án đúng (0=A, 1=B, 2=C...)</label>
        <input type="number" name="content[correct_answer]" value="{{ $correctAnswer }}" min="0" class="w-24 border rounded px-3 py-2">
    </div>
    <div>
        <label class="block font-medium text-gray-900 mb-1">Giải thích</label>
        <textarea name="content[explanation]" rows="2" class="w-full border rounded px-3 py-2" placeholder="Giải thích đáp án">{{ $explanation }}</textarea>
    </div>
</div>
<script>
function addDocHieuOption() {
    const list = document.getElementById('docheiu-options');
    const i = list.querySelectorAll('.option-row').length;
    const div = document.createElement('div');
    div.className = 'flex gap-2 option-row';
    div.innerHTML = '<span class="text-gray-500 mt-2">'+(i+1)+'.</span><input type="text" name="content[options]['+i+']" class="flex-1 border rounded px-2 py-1" placeholder="Đáp án"><button type="button" onclick="removeDocHieuOption(this)" class="text-red-600 px-2">✕</button>';
    list.appendChild(div);
    reindexDocHieuOptions();
}
function removeDocHieuOption(btn) {
    btn.closest('.option-row').remove();
    reindexDocHieuOptions();
}
function reindexDocHieuOptions() {
    document.querySelectorAll('#docheiu-options .option-row').forEach((row, i) => {
        row.querySelector('span').textContent = (i+1) + '.';
        row.querySelector('input').name = 'content[options][' + i + ']';
    });
}
</script>
