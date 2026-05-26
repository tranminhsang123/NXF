{{-- Editor cho luyen_doc: {passage, questions: [{question_number, question, options: [{text, romaji?, meaning?}], correct_answer, explanation}]} --}}
@php
    $passage = $content['passage'] ?? '';
    $questions = $content['questions'] ?? [];
@endphp
<div class="mt-6 space-y-6">
    <div class="border border-gray-200 rounded-lg p-4">
        <label class="block font-semibold text-gray-900 mb-2">Đoạn văn</label>
        <textarea name="content[passage]" rows="6" class="w-full border rounded px-3 py-2" placeholder="Nhập đoạn văn tiếng Nhật...">{{ $passage }}</textarea>
    </div>
    <div class="border border-gray-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <h3 class="font-semibold text-gray-900">Câu hỏi</h3>
            <button type="button" onclick="addQuestion()" class="text-sm bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">+ Thêm câu hỏi</button>
        </div>
        <div id="questions-list" class="space-y-6">
            @foreach($questions as $qi => $q)
            <div class="question-block p-4 bg-gray-50 rounded-lg border">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-medium">Câu {{ $qi + 1 }}</span>
                    <button type="button" onclick="removeQuestion(this)" class="text-red-600 hover:text-red-800 text-sm">✕ Xóa</button>
                </div>
                <input type="text" name="content[questions][{{ $qi }}][question_number]" value="{{ $q['question_number'] ?? '' }}" class="mb-2 w-full border rounded px-2 py-1 text-sm" placeholder="Số câu (vd: 1)">
                <input type="text" name="content[questions][{{ $qi }}][question]" value="{{ $q['question'] ?? '' }}" class="mb-2 w-full border rounded px-2 py-1" placeholder="Nội dung câu hỏi">
                <div class="mb-2">
                    <label class="text-xs text-gray-600">Đáp án (mỗi dòng 1 đáp án, format: text hoặc text|romaji|nghĩa)</label>
                    <textarea name="content[questions][{{ $qi }}][options_text]" rows="3" class="w-full border rounded px-2 py-1 text-sm" placeholder="Đáp án A&#10;Đáp án B">@foreach($q['options'] ?? [] as $opt){{ ($opt['text'] ?? '') }}{{ !empty($opt['romaji']) ? '|' . $opt['romaji'] : '' }}{{ !empty($opt['meaning']) ? '|' . ($opt['meaning'] ?? '') : '' }}
@endforeach</textarea>
                </div>
                <input type="number" name="content[questions][{{ $qi }}][correct_answer]" value="{{ $q['correct_answer'] ?? 0 }}" min="0" class="mb-2 w-20 border rounded px-2 py-1 text-sm" placeholder="0" title="Đáp án đúng (0=A, 1=B, ...)">
                <textarea name="content[questions][{{ $qi }}][explanation]" rows="2" class="w-full border rounded px-2 py-1 text-sm" placeholder="Giải thích">{{ $q['explanation'] ?? '' }}</textarea>
            </div>
            @endforeach
        </div>
    </div>
</div>
<script>
let questionCount = {{ count($questions) }};
function addQuestion() {
    const list = document.getElementById('questions-list');
    const div = document.createElement('div');
    div.className = 'question-block p-4 bg-gray-50 rounded-lg border';
    div.innerHTML = '<div class="flex justify-between items-center mb-2"><span class="font-medium">Câu '+(questionCount+1)+'</span><button type="button" onclick="removeQuestion(this)" class="text-red-600 hover:text-red-800 text-sm">✕ Xóa</button></div><input type="text" name="content[questions]['+questionCount+'][question_number]" class="mb-2 w-full border rounded px-2 py-1 text-sm" placeholder="Số câu"><input type="text" name="content[questions]['+questionCount+'][question]" class="mb-2 w-full border rounded px-2 py-1" placeholder="Nội dung câu hỏi"><div class="mb-2"><label class="text-xs text-gray-600">Đáp án (mỗi dòng 1 đáp án)</label><textarea name="content[questions]['+questionCount+'][options_text]" rows="3" class="w-full border rounded px-2 py-1 text-sm" placeholder="Đáp án A&#10;Đáp án B"></textarea></div><input type="number" name="content[questions]['+questionCount+'][correct_answer]" value="0" min="0" class="mb-2 w-20 border rounded px-2 py-1 text-sm" title="0=A, 1=B"><textarea name="content[questions]['+questionCount+'][explanation]" rows="2" class="w-full border rounded px-2 py-1 text-sm" placeholder="Giải thích"></textarea>';
    list.appendChild(div);
    questionCount++;
    reindexQuestions();
}
function removeQuestion(btn) {
    btn.closest('.question-block').remove();
    questionCount--;
    reindexQuestions();
}
function reindexQuestions() {
    document.querySelectorAll('#questions-list .question-block').forEach((blk, i) => {
        blk.querySelector('span.font-medium').textContent = 'Câu ' + (i+1);
        const prefix = 'content[questions]['+i+']';
        ['question_number','question','options_text','correct_answer','explanation'].forEach((key, j) => {
            const inp = blk.querySelectorAll('input, textarea')[j] || blk.querySelector('[name*="'+key+'"]');
            if (inp) inp.name = prefix + '[' + key + ']';
        });
        const inputs = blk.querySelectorAll('input, textarea');
        if (inputs[0]) inputs[0].name = prefix + '[question_number]';
        if (inputs[1]) inputs[1].name = prefix + '[question]';
        if (inputs[2]) inputs[2].name = prefix + '[options_text]';
        if (inputs[3]) inputs[3].name = prefix + '[correct_answer]';
        if (inputs[4]) inputs[4].name = prefix + '[explanation]';
    });
}
</script>
