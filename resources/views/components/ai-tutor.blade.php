@php
    $aiTutorActions = \App\Services\AiTutorService::actionLabels();
    $aiTutorUrl = auth()->check() ? route('minna.ai-tutor', ['number' => $lesson->number]) : null;
    $aiTutorLevel = auth()->check()
        ? (auth()->user()->placement_test_level ?: auth()->user()->onboarding_level ?: 'chưa rõ')
        : 'khách';
    $aiTutorGoal = auth()->check() ? (auth()->user()->jlpt_goal ?: 'chưa chọn JLPT') : '';
@endphp

<section id="ai-tutor-panel" class="mb-6 md:mb-8 rounded-2xl border border-blue-100 bg-white p-4 shadow-lg md:p-6">
    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wide text-blue-600">Trợ lý học tiếng Nhật</p>
            <h2 class="mt-1 text-xl font-bold text-gray-900">AI Tutor theo bài {{ $lesson->number }}</h2>
            <div class="mt-2 flex flex-wrap gap-2 text-xs font-semibold">
                <span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700">Trình độ: {{ $aiTutorLevel }}</span>
                @auth
                    <span class="rounded-full bg-green-50 px-3 py-1 text-green-700">Mục tiêu: {{ $aiTutorGoal }}</span>
                @endauth
            </div>
        </div>
        @guest
            <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700">
                Đăng nhập để dùng AI Tutor
            </a>
        @endguest
    </div>

    @auth
        <div class="mt-5 grid grid-cols-2 gap-2 md:grid-cols-4">
            @foreach($aiTutorActions as $action => $label)
                <button type="button"
                        class="ai-tutor-action rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 text-left text-sm font-bold text-gray-700 hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"
                        data-action="{{ $action }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <form id="ai-tutor-form" class="mt-5 grid grid-cols-1 gap-3 lg:grid-cols-[1fr,auto]">
            <textarea id="ai-tutor-prompt" rows="3" class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100" placeholder="Nhập câu cần dịch, câu tiếng Nhật cần sửa, hoặc chọn đoạn trong bài rồi bấm một thao tác."></textarea>
            <div class="flex flex-col gap-2">
                <button type="submit" class="rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white hover:bg-blue-700">
                    Hỏi trợ lý
                </button>
                <button type="button" id="ai-tutor-clear" class="rounded-xl bg-gray-100 px-5 py-3 text-sm font-bold text-gray-700 hover:bg-gray-200">
                    Xóa
                </button>
            </div>
        </form>

        <div id="ai-tutor-result" class="mt-5 hidden rounded-2xl border border-gray-200 bg-gray-50 p-4 md:p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p id="ai-tutor-result-title" class="text-lg font-extrabold text-gray-900"></p>
                    <p id="ai-tutor-result-meta" class="mt-1 text-xs font-semibold text-gray-500"></p>
                </div>
                <span id="ai-tutor-result-provider" class="rounded-full bg-white px-2 py-1 text-[11px] font-bold text-gray-600"></span>
            </div>
            <p id="ai-tutor-result-answer" class="mt-4 whitespace-pre-line text-sm leading-6 text-gray-700"></p>
            <ul id="ai-tutor-result-bullets" class="mt-4 hidden list-disc space-y-2 pl-5 text-sm text-gray-700"></ul>
            <div id="ai-tutor-result-examples" class="mt-4 hidden grid grid-cols-1 gap-2 md:grid-cols-2"></div>
            <div id="ai-tutor-result-quiz" class="mt-4 hidden space-y-3"></div>
        </div>
    @endauth
</section>

@auth
<script>
    (function () {
        const root = document.getElementById('ai-tutor-panel');
        if (!root) return;

        const tutorUrl = @json($aiTutorUrl);
        const csrfToken = @json(csrf_token());
        const form = document.getElementById('ai-tutor-form');
        const promptInput = document.getElementById('ai-tutor-prompt');
        const result = document.getElementById('ai-tutor-result');
        const resultTitle = document.getElementById('ai-tutor-result-title');
        const resultMeta = document.getElementById('ai-tutor-result-meta');
        const resultProvider = document.getElementById('ai-tutor-result-provider');
        const resultAnswer = document.getElementById('ai-tutor-result-answer');
        const bulletsNode = document.getElementById('ai-tutor-result-bullets');
        const examplesNode = document.getElementById('ai-tutor-result-examples');
        const quizNode = document.getElementById('ai-tutor-result-quiz');
        const clearButton = document.getElementById('ai-tutor-clear');
        const actionButtons = Array.from(root.querySelectorAll('.ai-tutor-action'));
        let currentAction = 'summarize_lesson';

        const promptActions = new Set(['check_translation', 'correct_sentence']);

        function selectedText() {
            const selection = window.getSelection ? String(window.getSelection()).trim() : '';
            return selection.length > 800 ? selection.slice(0, 800) : selection;
        }

        function setActive(button) {
            actionButtons.forEach((item) => {
                item.classList.remove('border-blue-500', 'bg-blue-600', 'text-white');
                item.classList.add('border-gray-200', 'bg-gray-50', 'text-gray-700');
            });
            button.classList.add('border-blue-500', 'bg-blue-600', 'text-white');
            button.classList.remove('border-gray-200', 'bg-gray-50', 'text-gray-700');
        }

        function setLoading(action) {
            result.classList.remove('hidden');
            resultTitle.textContent = 'Đang xử lý';
            resultMeta.textContent = action;
            resultProvider.textContent = '';
            resultAnswer.textContent = 'Trợ lý đang đọc ngữ cảnh bài và thông tin học của bạn...';
            bulletsNode.classList.add('hidden');
            examplesNode.classList.add('hidden');
            quizNode.classList.add('hidden');
            bulletsNode.innerHTML = '';
            examplesNode.innerHTML = '';
            quizNode.innerHTML = '';
        }

        function renderList(node, items) {
            node.innerHTML = '';
            if (!items || !items.length) {
                node.classList.add('hidden');
                return;
            }
            items.forEach((item) => {
                const li = document.createElement('li');
                li.textContent = item;
                node.appendChild(li);
            });
            node.classList.remove('hidden');
        }

        function renderExamples(items) {
            examplesNode.innerHTML = '';
            if (!items || !items.length) {
                examplesNode.classList.add('hidden');
                return;
            }
            items.forEach((item) => {
                const card = document.createElement('div');
                card.className = 'rounded-xl border border-gray-200 bg-white p-3';
                card.innerHTML = `
                    <p class="japanese-text font-bold text-gray-900"></p>
                    <p class="mt-1 text-sm text-gray-600"></p>
                `;
                card.children[0].textContent = item.jp || '';
                card.children[1].textContent = item.vi || '';
                examplesNode.appendChild(card);
            });
            examplesNode.classList.remove('hidden');
        }

        function renderQuiz(items) {
            quizNode.innerHTML = '';
            if (!items || !items.length) {
                quizNode.classList.add('hidden');
                return;
            }
            items.forEach((item, index) => {
                const card = document.createElement('div');
                card.className = 'rounded-xl border border-gray-200 bg-white p-3';
                const options = Array.isArray(item.options) && item.options.length
                    ? item.options.map((option) => `<span class="rounded-lg border border-gray-200 bg-gray-50 px-2 py-1 text-xs">${escapeHtml(option)}</span>`).join(' ')
                    : '';
                card.innerHTML = `
                    <p class="font-bold text-gray-900">${index + 1}. ${escapeHtml(item.prompt || '')}</p>
                    <div class="mt-2 flex flex-wrap gap-2">${options}</div>
                    <p class="mt-2 text-xs font-bold text-green-700">Đáp án: ${escapeHtml(item.answer || '')}</p>
                `;
                quizNode.appendChild(card);
            });
            quizNode.classList.remove('hidden');
        }

        function escapeHtml(value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        async function submitTutor(action) {
            currentAction = action || currentAction;
            setLoading(currentAction);

            try {
                const response = await fetch(tutorUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        action: currentAction,
                        prompt: promptInput.value || '',
                        selected_text: selectedText(),
                        section_key: document.querySelector('.section-content.active')?.dataset?.section || ''
                    })
                });

                const data = await response.json();
                if (!response.ok || !data.ok) {
                    throw new Error(data.message || 'Không xử lý được yêu cầu.');
                }

                const answer = data.answer || {};
                resultTitle.textContent = answer.title || 'Trợ lý học tiếng Nhật';
                resultMeta.textContent = answer.context_summary
                    ? `${answer.context_summary.lesson} • ${answer.context_summary.level} • ${answer.context_summary.goal}`
                    : '';
                resultProvider.textContent = answer.provider === 'openai' ? 'AI' : 'Local';
                resultAnswer.textContent = answer.answer || '';
                renderList(bulletsNode, answer.bullets || []);
                renderExamples(answer.examples || []);
                renderQuiz(answer.quiz || []);
            } catch (error) {
                resultTitle.textContent = 'Chưa trả lời được';
                resultMeta.textContent = '';
                resultProvider.textContent = '';
                resultAnswer.textContent = error.message || 'Vui lòng thử lại.';
            }
        }

        actionButtons.forEach((button) => {
            button.addEventListener('click', () => {
                currentAction = button.dataset.action || currentAction;
                setActive(button);
                if (promptActions.has(currentAction)) {
                    promptInput.focus();
                    return;
                }
                submitTutor(currentAction);
            });
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            submitTutor(currentAction);
        });

        clearButton.addEventListener('click', () => {
            promptInput.value = '';
            result.classList.add('hidden');
        });

        const defaultButton = actionButtons.find((button) => button.dataset.action === currentAction);
        if (defaultButton) {
            setActive(defaultButton);
        }
    })();
</script>
@endauth
