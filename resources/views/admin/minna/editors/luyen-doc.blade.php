@php
    $sentences = $content['sentences'] ?? [];
@endphp

<div class="border border-gray-200 rounded-lg p-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-gray-900">Câu luyện đọc</h3>
        <button type="button" onclick="addSentence()" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
            + Thêm câu
        </button>
    </div>
    <div id="sentences-list" class="space-y-2">
        @foreach($sentences as $i => $sent)
        <div class="flex flex-wrap sm:flex-nowrap gap-2 sentence-row">
            <span class="text-gray-500 mt-2">{{ $i + 1 }}.</span>
            <input type="text" name="content[sentences][{{ $i }}]" value="{{ is_array($sent) ? ($sent['text'] ?? $sent) : $sent }}" class="w-full sm:flex-1 border rounded px-3 py-2" placeholder="Nhập câu tiếng Nhật">
            <button type="button" onclick="removeSentence(this)" class="text-red-600 hover:text-red-800 px-2">✕</button>
        </div>
        @endforeach
    </div>
</div>

<script>
function addSentence() {
    const list = document.getElementById('sentences-list');
    const i = list.querySelectorAll('.sentence-row').length;
    const div = document.createElement('div');
    div.className = 'flex flex-wrap sm:flex-nowrap gap-2 sentence-row';
    div.innerHTML = '<span class="text-gray-500 mt-2">'+(i+1)+'.</span><input type="text" name="content[sentences]['+i+']" class="w-full sm:flex-1 border rounded px-3 py-2" placeholder="Nhập câu tiếng Nhật"><button type="button" onclick="removeSentence(this)" class="text-red-600 hover:text-red-800 px-2">✕</button>';
    list.appendChild(div);
    reindexSentences();
}

function removeSentence(btn) {
    btn.closest('.sentence-row').remove();
    reindexSentences();
}

function reindexSentences() {
    document.querySelectorAll('#sentences-list .sentence-row').forEach((row, i) => {
        row.querySelector('span').textContent = (i+1) + '.';
        row.querySelector('input').name = 'content[sentences][' + i + ']';
    });
}
</script>
