<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz nâng cao - {{ $lesson->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        .japanese-text {
            font-family: 'Hiragino Sans', 'Yu Gothic', 'Meiryo', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="container mx-auto max-w-5xl px-4 py-10">
        <nav class="mb-6 text-sm">
            <a href="{{ route('minna.show', ['number' => $lesson->number]) }}" class="font-semibold text-red-600 hover:text-red-700">
                Quay lại bài {{ $lesson->number }}
            </a>
        </nav>

        <section class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm mb-6">
            <p class="text-xs font-bold uppercase tracking-wide text-red-600">Quiz nâng cao</p>
            <h1 class="mt-2 text-2xl md:text-3xl font-bold text-gray-900">{{ $lesson->title }}</h1>
            <p class="mt-2 text-sm text-gray-600">Gồm điền nghĩa, viết lại tiếng Nhật, dịch nhanh và sắp xếp câu. Đạt 75% để qua bài.</p>
        </section>

        @if(session('warning'))
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-800">
                {{ session('warning') }}
            </div>
        @endif

        @if(empty($questions))
            <section class="rounded-2xl border border-gray-200 bg-white p-8 text-center shadow-sm">
                <p class="font-semibold text-gray-900">Bài này chưa đủ dữ liệu để tạo quiz nâng cao.</p>
                <p class="mt-2 text-sm text-gray-500">Cần ít nhất vài mục từ vựng hoặc mẫu câu có nghĩa tiếng Việt.</p>
            </section>
        @else
            <form method="POST" action="{{ route('minna.quiz.advanced.submit', ['number' => $lesson->number]) }}" class="space-y-5">
                @csrf

                @foreach($questions as $index => $question)
                    <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-gray-500">
                                    {{ $index + 1 }}. {{ str_replace('_', ' ', $question['type']) }}
                                </p>
                                <h2 class="mt-1 text-lg font-bold text-gray-900">{{ $question['prompt'] }}</h2>
                            </div>
                            @if(in_array($question['type'], ['fill_blank', 'translation', 'sentence_order'], true))
                                <button type="button"
                                        class="js-pronounce inline-flex items-center justify-center rounded-full border border-gray-300 px-3 py-1 text-xs font-bold text-gray-700 hover:border-red-400 hover:text-red-600"
                                        data-pronounce-text="{{ $question['type'] === 'sentence_order' ? $question['answer'] : $question['display'] }}">
                                    Nghe
                                </button>
                            @endif
                        </div>

                        <div class="mt-4 rounded-xl bg-gray-50 p-4">
                            <p class="{{ $question['type'] === 'rewrite' ? 'text-gray-800' : 'japanese-text text-xl text-gray-900' }}">
                                {{ $question['display'] }}
                            </p>
                            <p class="mt-2 text-xs text-gray-500">{{ $question['hint'] }}</p>
                        </div>

                        @if($question['type'] === 'sentence_order')
                            <input type="hidden" name="answers[{{ $index }}]" id="answer-{{ $index }}" required>
                            <div class="mt-4 flex flex-wrap gap-2" data-order-source="{{ $index }}">
                                @foreach($question['tokens'] as $token)
                                    <button type="button"
                                            class="order-token rounded-full border border-gray-300 bg-white px-3 py-2 text-sm font-semibold text-gray-800 hover:border-red-400"
                                            data-token="{{ $token }}">
                                        {{ $token }}
                                    </button>
                                @endforeach
                            </div>
                            <div class="mt-3 min-h-12 rounded-xl border border-dashed border-gray-300 bg-gray-50 p-3 text-sm text-gray-700" data-order-target="{{ $index }}"></div>
                            <button type="button" class="mt-2 text-xs font-bold text-red-600 hover:text-red-700" data-order-reset="{{ $index }}">Làm lại câu này</button>
                        @else
                            <input type="text"
                                   name="answers[{{ $index }}]"
                                   autocomplete="off"
                                   class="mt-4 w-full rounded-xl border border-gray-300 px-4 py-3 text-sm focus:border-red-500 focus:outline-none focus:ring-1 focus:ring-red-500"
                                   placeholder="Nhập câu trả lời"
                                   required>
                        @endif
                    </section>
                @endforeach

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('minna.show', ['number' => $lesson->number]) }}" class="rounded-xl border border-gray-300 px-5 py-3 text-sm font-bold text-gray-700 hover:border-gray-400">
                        Hủy
                    </a>
                    <button type="submit" class="rounded-xl bg-red-600 px-5 py-3 text-sm font-bold text-white hover:bg-red-700">
                        Nộp quiz
                    </button>
                </div>
            </form>
        @endif
    </main>

    @include('layouts.footer')

    <script>
        document.addEventListener('click', function (event) {
            const tokenButton = event.target.closest('.order-token');
            if (tokenButton) {
                const source = tokenButton.closest('[data-order-source]');
                const index = source?.getAttribute('data-order-source');
                const target = document.querySelector('[data-order-target="' + index + '"]');
                const input = document.getElementById('answer-' + index);
                if (!target || !input) return;

                const token = tokenButton.getAttribute('data-token') || '';
                const pill = document.createElement('span');
                pill.className = 'inline-flex rounded-full bg-red-50 px-2 py-1 mr-1 mb-1 text-sm font-semibold text-red-700';
                pill.textContent = token;
                target.appendChild(pill);
                input.value += token;
                tokenButton.disabled = true;
                tokenButton.classList.add('opacity-40');
                return;
            }

            const resetButton = event.target.closest('[data-order-reset]');
            if (resetButton) {
                const index = resetButton.getAttribute('data-order-reset');
                const target = document.querySelector('[data-order-target="' + index + '"]');
                const input = document.getElementById('answer-' + index);
                const source = document.querySelector('[data-order-source="' + index + '"]');
                if (target) target.innerHTML = '';
                if (input) input.value = '';
                source?.querySelectorAll('.order-token').forEach(function (button) {
                    button.disabled = false;
                    button.classList.remove('opacity-40');
                });
            }
        });
    </script>
</body>
</html>
