{{-- Editor cho nguPhap: [{particle, explanation, examples: [{japanese, vietnamese}]}] --}}
@php
    $raw = is_array($content) ? $content : [];
    if ($raw === []) {
        $grammarPoints = [];
    } elseif (\Illuminate\Support\Arr::isList($raw)) {
        $grammarPoints = $raw;
    } elseif (isset($raw['particle']) || isset($raw['explanation']) || isset($raw['examples'])) {
        $grammarPoints = [$raw];
    } else {
        $grammarPoints = array_values(array_filter($raw, fn ($v) => is_array($v)));
    }
@endphp
<div class="mt-6 border border-gray-200 rounded-lg p-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-semibold text-gray-900">Điểm ngữ pháp</h3>
        <button type="button" onclick="addGrammarPoint()" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">+ Thêm điểm ngữ pháp</button>
    </div>
    <div id="grammar-list" class="space-y-6">
        @foreach($grammarPoints as $gp)
        <div class="grammar-point p-4 bg-gray-50 rounded-lg border">
            <div class="flex justify-between items-center mb-2">
                <span class="font-medium">Điểm {{ $loop->iteration }}</span>
                <button type="button" onclick="removeGrammarPoint(this)" class="text-red-600 hover:text-red-800 text-sm">✕ Xóa</button>
            </div>
            <input type="text" name="content[{{ $loop->index }}][particle]" value="{{ $gp['particle'] ?? '' }}" class="mb-2 w-full border rounded px-2 py-1" placeholder="Trợ từ (vd: ~は)">
            <textarea name="content[{{ $loop->index }}][explanation]" rows="2" class="mb-2 w-full border rounded px-2 py-1 text-sm" placeholder="Giải thích">{{ $gp['explanation'] ?? '' }}</textarea>
            <div class="mb-2">
                <label class="text-xs text-gray-600">Ví dụ (mỗi dòng: Tiếng Nhật|Tiếng Việt)</label>
                <textarea name="content[{{ $loop->index }}][examples_text]" rows="4" class="w-full border rounded px-2 py-1 text-sm" placeholder="わたしはタスです。|Tôi là Tasu.">@foreach($gp['examples'] ?? [] as $ex){{ ($ex['japanese'] ?? '') }}|{{ ($ex['vietnamese'] ?? '') }}
@endforeach</textarea>
            </div>
        </div>
        @endforeach
    </div>
</div>
<script>
let grammarCount = {{ count($grammarPoints) }};
function addGrammarPoint() {
    const list = document.getElementById('grammar-list');
    const div = document.createElement('div');
    div.className = 'grammar-point p-4 bg-gray-50 rounded-lg border';
    div.innerHTML = '<div class="flex justify-between items-center mb-2"><span class="font-medium">Điểm '+(grammarCount+1)+'</span><button type="button" onclick="removeGrammarPoint(this)" class="text-red-600 hover:text-red-800 text-sm">✕ Xóa</button></div><input type="text" name="content['+grammarCount+'][particle]" class="mb-2 w-full border rounded px-2 py-1" placeholder="Trợ từ"><textarea name="content['+grammarCount+'][explanation]" rows="2" class="mb-2 w-full border rounded px-2 py-1 text-sm" placeholder="Giải thích"></textarea><div class="mb-2"><label class="text-xs text-gray-600">Ví dụ (mỗi dòng: Tiếng Nhật|Tiếng Việt)</label><textarea name="content['+grammarCount+'][examples_text]" rows="4" class="w-full border rounded px-2 py-1 text-sm" placeholder="わたしはタスです。|Tôi là Tasu."></textarea></div>';
    list.appendChild(div);
    grammarCount++;
    reindexGrammar();
}
function removeGrammarPoint(btn) {
    btn.closest('.grammar-point').remove();
    grammarCount--;
    reindexGrammar();
}
function reindexGrammar() {
    document.querySelectorAll('#grammar-list .grammar-point').forEach((blk, i) => {
        blk.querySelector('span.font-medium').textContent = 'Điểm ' + (i+1);
        ['particle','explanation','examples_text'].forEach((key, j) => {
            const inp = blk.querySelectorAll('input, textarea')[j];
            if (inp) inp.name = 'content['+i+']['+key+']';
        });
    });
}
</script>
