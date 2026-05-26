@php
    $hanTuList = is_array($content) && !isset($content['kanji']) ? $content : ($content['items'] ?? []);
@endphp

<div class="border border-gray-200 rounded-lg p-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-gray-900">Hán tự (Kanji)</h3>
        <button type="button" onclick="addHanTu()" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
            + Thêm
        </button>
    </div>
    <div id="hantu-list" class="space-y-3">
        @foreach($hanTuList as $i => $item)
        @php $it = is_array($item) ? $item : []; @endphp
        <div class="grid grid-cols-2 md:grid-cols-6 gap-2 hantu-row p-3 bg-gray-50 rounded">
            <input type="text" name="content[{{ $i }}][kanji]" value="{{ $it['kanji'] ?? '' }}" class="border rounded px-2 py-1 text-lg text-center" placeholder="字">
            <input type="text" name="content[{{ $i }}][han_viet]" value="{{ $it['han_viet'] ?? '' }}" class="border rounded px-2 py-1" placeholder="Hán Việt">
            <input type="text" name="content[{{ $i }}][nghia_vi]" value="{{ $it['nghia_vi'] ?? '' }}" class="border rounded px-2 py-1" placeholder="Nghĩa">
            <input type="text" name="content[{{ $i }}][tu_vung]" value="{{ $it['tu_vung'] ?? '' }}" class="border rounded px-2 py-1" placeholder="Từ vựng">
            <input type="text" name="content[{{ $i }}][onyomi]" value="{{ is_array($it['onyomi'] ?? null) ? implode(',', $it['onyomi']) : ($it['onyomi'] ?? '') }}" class="border rounded px-2 py-1" placeholder="Onyomi (cách đọc ON)">
            <div class="flex items-center gap-1">
                <input type="text" name="content[{{ $i }}][kunyomi]" value="{{ is_array($it['kunyomi'] ?? null) ? implode(',', $it['kunyomi']) : ($it['kunyomi'] ?? '') }}" class="border rounded px-2 py-1 flex-1" placeholder="Kunyomi">
                <button type="button" onclick="removeHanTu(this)" class="text-red-600 hover:text-red-800">✕</button>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
function addHanTu() {
    const list = document.getElementById('hantu-list');
    const i = list.querySelectorAll('.hantu-row').length;
    const div = document.createElement('div');
    div.className = 'grid grid-cols-2 md:grid-cols-6 gap-2 hantu-row p-3 bg-gray-50 rounded';
    div.innerHTML = '<input type="text" name="content['+i+'][kanji]" class="border rounded px-2 py-1 text-lg text-center" placeholder="字"><input type="text" name="content['+i+'][han_viet]" class="border rounded px-2 py-1" placeholder="Hán Việt"><input type="text" name="content['+i+'][nghia_vi]" class="border rounded px-2 py-1" placeholder="Nghĩa"><input type="text" name="content['+i+'][tu_vung]" class="border rounded px-2 py-1" placeholder="Từ vựng"><input type="text" name="content['+i+'][onyomi]" class="border rounded px-2 py-1" placeholder="Onyomi"><div class="flex items-center gap-1"><input type="text" name="content['+i+'][kunyomi]" class="border rounded px-2 py-1 flex-1" placeholder="Kunyomi"><button type="button" onclick="removeHanTu(this)" class="text-red-600 hover:text-red-800">✕</button></div>';
    list.appendChild(div);
    reindexHanTu();
}

function removeHanTu(btn) {
    btn.closest('.hantu-row').remove();
    reindexHanTu();
}

function reindexHanTu() {
    document.querySelectorAll('#hantu-list .hantu-row').forEach((row, i) => {
        row.querySelectorAll('input').forEach(inp => {
            inp.name = inp.name.replace(/^content\[\d+\]/, 'content['+i+']');
        });
    });
}
</script>
