@php
    $dialogue = $content['dialogue'] ?? [];
@endphp

<div class="border border-gray-200 rounded-lg p-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-gray-900">Hội thoại</h3>
        <button type="button" onclick="addDialogueLine()" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
            + Thêm dòng
        </button>
    </div>
    <p class="text-xs text-gray-500 mb-2">Speaker: A, B, C... hoặc — (để ngăn cách)</p>
    <div id="dialogue-list" class="space-y-2">
        @foreach($dialogue as $i => $line)
        <div class="flex gap-2 dialogue-row flex-wrap">
            <input type="text" name="content[dialogue][{{ $i }}][speaker]" value="{{ $line['speaker'] ?? '' }}" class="w-14 sm:w-16 border rounded px-2 py-1" placeholder="A">
            <input type="text" name="content[dialogue][{{ $i }}][jp]" value="{{ $line['jp'] ?? '' }}" class="w-full sm:flex-1 sm:min-w-[200px] border rounded px-2 py-1" placeholder="Tiếng Nhật">
            <input type="text" name="content[dialogue][{{ $i }}][romaji]" value="{{ $line['romaji'] ?? '' }}" class="w-full sm:flex-1 sm:min-w-[150px] border rounded px-2 py-1" placeholder="Romaji (tùy chọn)">
            <button type="button" onclick="removeDialogueLine(this)" class="text-red-600 hover:text-red-800 px-2">✕</button>
        </div>
        @endforeach
    </div>
</div>

<script>
function addDialogueLine() {
    const list = document.getElementById('dialogue-list');
    const i = list.querySelectorAll('.dialogue-row').length;
    const div = document.createElement('div');
    div.className = 'flex gap-2 dialogue-row flex-wrap';
    div.innerHTML = '<input type="text" name="content[dialogue]['+i+'][speaker]" class="w-14 sm:w-16 border rounded px-2 py-1" placeholder="A"><input type="text" name="content[dialogue]['+i+'][jp]" class="w-full sm:flex-1 sm:min-w-[200px] border rounded px-2 py-1" placeholder="Tiếng Nhật"><input type="text" name="content[dialogue]['+i+'][romaji]" class="w-full sm:flex-1 sm:min-w-[150px] border rounded px-2 py-1" placeholder="Romaji"><button type="button" onclick="removeDialogueLine(this)" class="text-red-600 hover:text-red-800 px-2">✕</button>';
    list.appendChild(div);
    reindexDialogue();
}

function removeDialogueLine(btn) {
    btn.closest('.dialogue-row').remove();
    reindexDialogue();
}

function reindexDialogue() {
    document.querySelectorAll('#dialogue-list .dialogue-row').forEach((row, i) => {
        row.querySelectorAll('input').forEach((inp, j) => {
            const key = ['speaker','jp','romaji'][j];
            inp.name = 'content[dialogue]['+i+']['+key+']';
        });
    });
}
</script>
