{{-- Editor cho marugoto_n5: {tuVung: [words], nguPhap: [grammar_points]} --}}
@php
    $tuVung = $content['tuVung'] ?? [];
    $nguPhap = $content['nguPhap'] ?? [];
@endphp
<div class="mt-6 space-y-6">
    <div class="border border-gray-200 rounded-lg p-4">
        <h3 class="font-semibold text-gray-900 mb-3">Từ vựng</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50"><tr><th class="px-2 py-2 text-left">Từ</th><th class="px-2 py-2 text-left">Nghĩa</th><th class="w-10"></th></tr></thead>
                <tbody id="marugoto-words-tbody">
                    @foreach($tuVung as $i => $w)
                    <tr class="mw-row">
                        <td class="px-2 py-2"><input type="text" name="content[tuVung][{{ $i }}][tu]" value="{{ $w['tu'] ?? '' }}" class="w-full border rounded px-2 py-1"></td>
                        <td class="px-2 py-2"><input type="text" name="content[tuVung][{{ $i }}][nghia]" value="{{ $w['nghia'] ?? '' }}" class="w-full border rounded px-2 py-1"></td>
                        <td><button type="button" onclick="removeMarugotoWord(this)" class="text-red-600">✕</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <button type="button" onclick="addMarugotoWord()" class="mt-2 text-sm bg-green-600 text-white px-3 py-1 rounded">+ Thêm từ</button>
    </div>
    <div class="border border-gray-200 rounded-lg p-4">
        <h3 class="font-semibold text-gray-900 mb-3">Ngữ pháp</h3>
        <div id="marugoto-grammar-list" class="space-y-3">
            @foreach($nguPhap as $gi => $gp)
            <div class="mg-point p-3 bg-gray-50 rounded border">
                <div class="flex justify-between mb-1"><span class="font-medium">Điểm {{ $gi + 1 }}</span><button type="button" onclick="removeMarugotoGrammar(this)" class="text-red-600 text-sm">✕</button></div>
                <input type="text" name="content[nguPhap][{{ $gi }}][particle]" value="{{ $gp['particle'] ?? '' }}" class="mb-2 w-full border rounded px-2 py-1 text-sm" placeholder="Trợ từ">
                <textarea name="content[nguPhap][{{ $gi }}][explanation]" rows="2" class="mb-2 w-full border rounded px-2 py-1 text-sm" placeholder="Giải thích">{{ $gp['explanation'] ?? '' }}</textarea>
                <textarea name="content[nguPhap][{{ $gi }}][examples_text]" rows="3" class="w-full border rounded px-2 py-1 text-sm" placeholder="Ví dụ (mỗi dòng: JP|VN)">@foreach($gp['examples'] ?? [] as $ex){{ ($ex['japanese'] ?? '') }}|{{ ($ex['vietnamese'] ?? '') }}
@endforeach</textarea>
            </div>
            @endforeach
        </div>
        <button type="button" onclick="addMarugotoGrammar()" class="mt-2 text-sm bg-green-600 text-white px-3 py-1 rounded">+ Thêm điểm ngữ pháp</button>
    </div>
</div>
<script>
function addMarugotoWord() {
    const tbody = document.getElementById('marugoto-words-tbody');
    const i = tbody.querySelectorAll('.mw-row').length;
    const tr = document.createElement('tr'); tr.className = 'mw-row';
    tr.innerHTML = '<td class="px-2 py-2"><input type="text" name="content[tuVung]['+i+'][tu]" class="w-full border rounded px-2 py-1"></td><td class="px-2 py-2"><input type="text" name="content[tuVung]['+i+'][nghia]" class="w-full border rounded px-2 py-1"></td><td><button type="button" onclick="removeMarugotoWord(this)" class="text-red-600">✕</button></td>';
    tbody.appendChild(tr);
}
function removeMarugotoWord(btn) { btn.closest('tr').remove(); }
function addMarugotoGrammar() {
    const list = document.getElementById('marugoto-grammar-list');
    const i = list.querySelectorAll('.mg-point').length;
    const div = document.createElement('div'); div.className = 'mg-point p-3 bg-gray-50 rounded border';
    div.innerHTML = '<div class="flex justify-between mb-1"><span class="font-medium">Điểm '+(i+1)+'</span><button type="button" onclick="removeMarugotoGrammar(this)" class="text-red-600 text-sm">✕</button></div><input type="text" name="content[nguPhap]['+i+'][particle]" class="mb-2 w-full border rounded px-2 py-1 text-sm" placeholder="Trợ từ"><textarea name="content[nguPhap]['+i+'][explanation]" rows="2" class="mb-2 w-full border rounded px-2 py-1 text-sm" placeholder="Giải thích"></textarea><textarea name="content[nguPhap]['+i+'][examples_text]" rows="3" class="w-full border rounded px-2 py-1 text-sm" placeholder="Ví dụ"></textarea>';
    list.appendChild(div);
}
function removeMarugotoGrammar(btn) { btn.closest('.mg-point').remove(); }
</script>
