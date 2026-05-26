{{-- Editor cho tuVung: words = [{tu, nghia}] --}}
@php
    $words = is_array($content) ? $content : [];
@endphp
<div class="mt-6 border border-gray-200 rounded-lg p-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-gray-900">Từ vựng</h3>
        <button type="button" onclick="addWordRow()" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">+ Thêm từ</button>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-left font-medium text-gray-700">Từ</th>
                    <th class="px-3 py-2 text-left font-medium text-gray-700">Nghĩa</th>
                    <th class="px-3 py-2 w-10"></th>
                </tr>
            </thead>
            <tbody id="words-tbody">
                @foreach($words as $i => $w)
                <tr class="word-row">
                    <td class="px-3 py-2"><input type="text" name="content[{{ $i }}][tu]" value="{{ $w['tu'] ?? '' }}" class="w-full border rounded px-2 py-1" placeholder="わたし"></td>
                    <td class="px-3 py-2"><input type="text" name="content[{{ $i }}][nghia]" value="{{ $w['nghia'] ?? '' }}" class="w-full border rounded px-2 py-1" placeholder="nghĩa"></td>
                    <td class="px-3 py-2"><button type="button" onclick="removeWordRow(this)" class="text-red-600 hover:text-red-800">✕</button></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<script>
function addWordRow() {
    const tbody = document.getElementById('words-tbody');
    const i = tbody.querySelectorAll('.word-row').length;
    const tr = document.createElement('tr');
    tr.className = 'word-row';
    tr.innerHTML = '<td class="px-3 py-2"><input type="text" name="content['+i+'][tu]" class="w-full border rounded px-2 py-1" placeholder="わたし"></td><td class="px-3 py-2"><input type="text" name="content['+i+'][nghia]" class="w-full border rounded px-2 py-1" placeholder="nghĩa"></td><td class="px-3 py-2"><button type="button" onclick="removeWordRow(this)" class="text-red-600 hover:text-red-800">✕</button></td>';
    tbody.appendChild(tr);
    reindexWords();
}
function removeWordRow(btn) {
    btn.closest('tr').remove();
    reindexWords();
}
function reindexWords() {
    document.querySelectorAll('#words-tbody .word-row').forEach((tr, i) => {
        tr.querySelectorAll('input').forEach((inp, j) => {
            inp.name = 'content['+i+'][' + (j === 0 ? 'tu' : 'nghia') + ']';
        });
    });
}
</script>
