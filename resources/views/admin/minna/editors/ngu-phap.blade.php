@php
    $raw = is_array($content) ? $content : [];
    if ($raw === []) {
        $grammarPoints = [[]];
    } elseif (\Illuminate\Support\Arr::isList($raw)) {
        $grammarPoints = $raw;
    } elseif (isset($raw['title']) || isset($raw['pattern']) || isset($raw['explain']) || isset($raw['notes']) || isset($raw['examples'])) {
        $grammarPoints = [$raw];
    } else {
        $grammarPoints = array_values(array_filter($raw, fn ($v) => is_array($v)));
        if ($grammarPoints === []) {
            $grammarPoints = [[]];
        }
    }
@endphp
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="font-semibold text-gray-900">Điểm ngữ pháp</h3>
        <button type="button" onclick="addGrammarPoint()" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">+ Thêm điểm ngữ pháp</button>
    </div>
    <div id="nguphap-list" class="space-y-8">
        @foreach($grammarPoints as $gp)
        <div class="nguphap-block border border-gray-200 rounded-lg p-4 bg-gray-50">
            <div class="flex justify-between items-center mb-4">
                <span class="font-medium text-gray-800">Điểm {{ $loop->iteration }}</span>
                <button type="button" onclick="removeGrammarPoint(this)" class="text-red-600 hover:text-red-800 text-sm">✕ Xóa</button>
            </div>
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label>
                    <input type="text" name="content[{{ $loop->index }}][title]" value="{{ $gp['title'] ?? '' }}" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="vd: Phần 1: Trợ từ は">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cấu trúc (pattern)</label>
                    <p class="text-xs text-gray-500 mb-1">Một dòng = chuỗi đơn. Nhiều dòng "key: value" = object (vd: affirm: N です)</p>
                    <textarea name="content[{{ $loop->index }}][pattern_text]" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="affirm: N です&#10;negate: N じゃありません">@php
                        $pat = $gp['pattern'] ?? null;
                        if (is_array($pat)) {
                            foreach ($pat as $pk => $pv) { echo e($pk) . ': ' . e($pv) . "\n"; }
                        } elseif (is_string($pat)) { echo e($pat); }
                    @endphp</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Giải thích (explain)</label>
                    <p class="text-xs text-gray-500 mb-1">Mỗi dòng = một ý. Hoặc "key: nội dung" cho mục có nhãn</p>
                    <textarea name="content[{{ $loop->index }}][explain_text]" rows="4" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="〜は đặt sau danh từ...">@php
                        $expl = $gp['explain'] ?? null;
                        if (is_array($expl)) {
                            foreach ($expl as $ek => $ev) { echo (is_numeric($ek) ? '' : $ek . ': ') . e($ev) . "\n"; }
                        } elseif (is_string($expl)) { echo e($expl); }
                    @endphp</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lưu ý (notes) – mỗi dòng 1 ý</label>
                    <textarea name="content[{{ $loop->index }}][notes_text]" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="じゃありません dùng trong hội thoại...">@foreach($gp['notes'] ?? [] as $n){{ $n }}
@endforeach</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ví dụ – mỗi dòng: Tiếng Nhật|Nghĩa</label>
                    <textarea name="content[{{ $loop->index }}][examples_text]" rows="4" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="わたしは がくせいです。|Tôi là học sinh.">@foreach($gp['examples'] ?? [] as $ex){{ ($ex['jp'] ?? '') }}|{{ ($ex['nghia'] ?? '') }}
@endforeach</textarea>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
<script>
let nguphapCount = {{ count($grammarPoints) }};
function addGrammarPoint() {
    const list = document.getElementById('nguphap-list');
    const i = nguphapCount;
    const div = document.createElement('div');
    div.className = 'nguphap-block border border-gray-200 rounded-lg p-4 bg-gray-50';
    div.innerHTML = '<div class="flex justify-between items-center mb-4"><span class="font-medium text-gray-800">Điểm '+(i+1)+'</span><button type="button" onclick="removeGrammarPoint(this)" class="text-red-600 hover:text-red-800 text-sm">✕ Xóa</button></div>' +
        '<div class="grid grid-cols-1 gap-4">' +
        '<div><label class="block text-sm font-medium text-gray-700 mb-1">Tiêu đề</label><input type="text" name="content['+i+'][title]" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="vd: Phần 1: Trợ từ は"></div>' +
        '<div><label class="block text-sm font-medium text-gray-700 mb-1">Cấu trúc (pattern)</label><textarea name="content['+i+'][pattern_text]" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-sm" placeholder="key: value hoặc chuỗi đơn"></textarea></div>' +
        '<div><label class="block text-sm font-medium text-gray-700 mb-1">Giải thích (explain)</label><textarea name="content['+i+'][explain_text]" rows="4" class="w-full border border-gray-300 rounded px-3 py-2 text-sm"></textarea></div>' +
        '<div><label class="block text-sm font-medium text-gray-700 mb-1">Lưu ý (notes)</label><textarea name="content['+i+'][notes_text]" rows="2" class="w-full border border-gray-300 rounded px-3 py-2 text-sm"></textarea></div>' +
        '<div><label class="block text-sm font-medium text-gray-700 mb-1">Ví dụ (JP|Nghĩa mỗi dòng)</label><textarea name="content['+i+'][examples_text]" rows="4" class="w-full border border-gray-300 rounded px-3 py-2 text-sm"></textarea></div>' +
        '</div>';
    list.appendChild(div);
    nguphapCount++;
    reindexNguphap();
}
function removeGrammarPoint(btn) {
    btn.closest('.nguphap-block').remove();
    nguphapCount--;
    reindexNguphap();
}
function reindexNguphap() {
    document.querySelectorAll('#nguphap-list .nguphap-block').forEach((blk, i) => {
        blk.querySelector('span.font-medium').textContent = 'Điểm ' + (i+1);
        ['title','pattern_text','explain_text','notes_text','examples_text'].forEach((key, j) => {
            const inp = blk.querySelectorAll('input, textarea')[j];
            if (inp) inp.name = 'content['+i+']['+key+']';
        });
    });
}
</script>
