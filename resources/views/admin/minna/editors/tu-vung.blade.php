@php
    $vocabCats = [
        'vocab' => ['label' => 'Từ vựng', 'fields' => ['tu_vung', 'han_tu', 'am_han', 'nghia', 'loai_tu', 'ghi_chu'], 'loai_tu_options' => ['danh_tu' => 'Danh từ', 'dong_tu' => 'Động từ', 'tinh_tu' => 'Tính từ']],
        'mau_cau' => ['label' => 'Mẫu câu', 'fields' => ['jp', 'nghia']],
        'countries' => ['label' => 'Tên nước', 'fields' => ['tu_vung', 'nghia']],
        'proper_nouns' => ['label' => 'Danh từ riêng', 'fields' => ['tu_vung', 'nghia']],
        'cau' => ['label' => 'Câu', 'fields' => ['jp', 'nghia']],
        'places' => ['label' => 'Địa danh', 'fields' => ['tu_vung', 'nghia']],
        'rail' => ['label' => 'Từ vựng tàu', 'fields' => ['tu_vung', 'nghia']],
    ];
@endphp

<div class="space-y-8">
    @foreach($vocabCats as $catKey => $cat)
    <div class="border border-gray-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-900">{{ $cat['label'] }}</h3>
            <button type="button" onclick="addRow('{{ $catKey }}')" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                + Thêm dòng
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        @if(in_array('tu_vung', $cat['fields']))
                            <th class="px-2 py-2 text-left font-medium text-gray-700">Từ vựng</th>
                        @endif
                        @if(in_array('han_tu', $cat['fields']))
                            <th class="px-2 py-2 text-left font-medium text-gray-700">Hán tự</th>
                        @endif
                        @if(in_array('am_han', $cat['fields']))
                            <th class="px-2 py-2 text-left font-medium text-gray-700">Âm Hán</th>
                        @endif
                        @if(in_array('jp', $cat['fields']))
                            <th class="px-2 py-2 text-left font-medium text-gray-700">Tiếng Nhật</th>
                        @endif
                        <th class="px-2 py-2 text-left font-medium text-gray-700">Nghĩa</th>
                        @if(in_array('loai_tu', $cat['fields']))
                            <th class="px-2 py-2 text-left font-medium text-gray-700">Loại từ</th>
                        @endif
                        @if(in_array('ghi_chu', $cat['fields']))
                            <th class="px-2 py-2 text-left font-medium text-gray-700">Ghi chú</th>
                        @endif
                        <th class="px-2 py-2 w-12"></th>
                    </tr>
                </thead>
                <tbody id="tbody-{{ $catKey }}" class="divide-y divide-gray-200">
                    @foreach(($content[$catKey] ?? []) as $i => $row)
                    <tr class="vocab-row">
                        @if(in_array('tu_vung', $cat['fields']))
                            <td class="px-2 py-2"><input type="text" name="content[{{ $catKey }}][{{ $i }}][tu_vung]" value="{{ $row['tu_vung'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="わたし"></td>
                        @endif
                        @if(in_array('han_tu', $cat['fields']))
                            <td class="px-2 py-2"><input type="text" name="content[{{ $catKey }}][{{ $i }}][han_tu]" value="{{ $row['han_tu'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="私"></td>
                        @endif
                        @if(in_array('am_han', $cat['fields']))
                            <td class="px-2 py-2"><input type="text" name="content[{{ $catKey }}][{{ $i }}][am_han]" value="{{ $row['am_han'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="NHÂN"></td>
                        @endif
                        @if(in_array('jp', $cat['fields']))
                            <td class="px-2 py-2"><input type="text" name="content[{{ $catKey }}][{{ $i }}][jp]" value="{{ $row['jp'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="はじめまして"></td>
                        @endif
                        <td class="px-2 py-2"><input type="text" name="content[{{ $catKey }}][{{ $i }}][nghia]" value="{{ $row['nghia'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="nghĩa" required></td>
                        @if(in_array('loai_tu', $cat['fields']))
                            <td class="px-2 py-2">
                                <select name="content[{{ $catKey }}][{{ $i }}][loai_tu]" class="border rounded px-2 py-1 text-sm">
                                    <option value="">—</option>
                                    @foreach($cat['loai_tu_options'] ?? [] as $optVal => $optLabel)
                                        <option value="{{ $optVal }}" {{ ($row['loai_tu'] ?? '') == $optVal ? 'selected' : '' }}>{{ $optLabel }}</option>
                                    @endforeach
                                </select>
                            </td>
                        @endif
                        @if(in_array('ghi_chu', $cat['fields']))
                            <td class="px-2 py-2"><input type="text" name="content[{{ $catKey }}][{{ $i }}][ghi_chu]" value="{{ $row['ghi_chu'] ?? '' }}" class="w-full border rounded px-2 py-1 text-sm" placeholder="ghi chú"></td>
                        @endif
                        <td class="px-2 py-2"><button type="button" onclick="removeRow(this)" class="text-red-600 hover:text-red-800">✕</button></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>

<script>
const vocabCats = @json($vocabCats);
let rowCount = @json(array_map('count', array_map(fn($c) => $content[$c] ?? [], array_keys($vocabCats))));

function addRow(catKey) {
    const tbody = document.getElementById('tbody-' + catKey);
    const cat = vocabCats[catKey];
    const i = (tbody.querySelectorAll('tr').length);
    let html = '<tr class="vocab-row">';
    if (cat.fields.includes('tu_vung')) html += '<td class="px-2 py-2"><input type="text" name="content['+catKey+']['+i+'][tu_vung]" class="w-full border rounded px-2 py-1 text-sm" placeholder="わたし"></td>';
    if (cat.fields.includes('han_tu')) html += '<td class="px-2 py-2"><input type="text" name="content['+catKey+']['+i+'][han_tu]" class="w-full border rounded px-2 py-1 text-sm"></td>';
    if (cat.fields.includes('am_han')) html += '<td class="px-2 py-2"><input type="text" name="content['+catKey+']['+i+'][am_han]" class="w-full border rounded px-2 py-1 text-sm"></td>';
    if (cat.fields.includes('jp')) html += '<td class="px-2 py-2"><input type="text" name="content['+catKey+']['+i+'][jp]" class="w-full border rounded px-2 py-1 text-sm"></td>';
    html += '<td class="px-2 py-2"><input type="text" name="content['+catKey+']['+i+'][nghia]" class="w-full border rounded px-2 py-1 text-sm" placeholder="nghĩa" required></td>';
    if (cat.fields.includes('loai_tu')) {
        html += '<td class="px-2 py-2"><select name="content['+catKey+']['+i+'][loai_tu]" class="border rounded px-2 py-1 text-sm"><option value="">—</option><option value="danh_tu">Danh từ</option><option value="dong_tu">Động từ</option><option value="tinh_tu">Tính từ</option></select></td>';
    }
    if (cat.fields.includes('ghi_chu')) html += '<td class="px-2 py-2"><input type="text" name="content['+catKey+']['+i+'][ghi_chu]" class="w-full border rounded px-2 py-1 text-sm"></td>';
    html += '<td class="px-2 py-2"><button type="button" onclick="removeRow(this)" class="text-red-600 hover:text-red-800">✕</button></td></tr>';
    tbody.insertAdjacentHTML('beforeend', html);
    reindexRows(catKey);
}

function removeRow(btn) {
    const tbody = btn.closest('tbody');
    const catKey = tbody ? tbody.id.replace('tbody-', '') : '';
    btn.closest('tr').remove();
    if (catKey) reindexRows(catKey);
}

function reindexRows(catKey) {
    const tbody = document.getElementById('tbody-' + catKey);
    if (!tbody) return;
    const escaped = catKey.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    tbody.querySelectorAll('tr').forEach((tr, i) => {
        tr.querySelectorAll('input, select').forEach(inp => {
            if (inp.name) inp.name = inp.name.replace(new RegExp('(content\\[' + escaped + '\\])\\[\\d+\\]'), '$1[' + i + ']');
        });
    });
}
</script>
