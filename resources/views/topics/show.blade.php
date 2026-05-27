<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $topic['title'] }} - Chủ đề thực tế</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="min-h-screen pt-24 pb-12">
        <div class="container mx-auto max-w-7xl px-4">
            <div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <a href="{{ route('topics.index') }}" class="text-sm font-bold text-red-600 hover:text-red-700">← Chủ đề thực tế</a>
                <div class="flex flex-wrap gap-2">
                    @foreach($topic['tags'] as $tag)
                        <span class="rounded-full bg-red-50 px-3 py-1 text-xs font-bold text-red-700">{{ str_replace('_', ' ', $tag) }}</span>
                    @endforeach
                </div>
            </div>

            <section class="mb-8 overflow-hidden rounded-2xl border border-slate-800 bg-slate-950 text-white shadow-sm">
                <div class="grid gap-0 lg:grid-cols-[1.1fr_0.9fr]">
                    <div class="p-6 md:p-8">
                        <p class="text-sm font-bold uppercase tracking-wide text-red-200">{{ $topic['level'] }} · {{ $topic['duration_minutes'] }} phút</p>
                        <h1 class="mt-3 text-3xl font-extrabold tracking-tight md:text-4xl">{{ $topic['title'] }}</h1>
                        <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">{{ $topic['subtitle'] }}</p>
                        <p class="mt-5 rounded-xl bg-white/10 p-4 text-sm font-semibold leading-6 text-slate-100">{{ $topic['goal'] }}</p>
                        <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                            <a href="#mini-task" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-3 text-sm font-bold text-white hover:bg-red-700">
                                Mini task 5 phút
                            </a>
                            <button type="button" data-speak="{{ $topic['first_audio_text'] }}" class="speak-button inline-flex items-center justify-center rounded-lg bg-white/10 px-5 py-3 text-sm font-bold text-white hover:bg-white/15" title="Nghe mẫu">
                                ▶ Nghe mẫu
                            </button>
                        </div>
                    </div>
                    <div class="border-t border-white/10 bg-white/[0.04] p-6 md:p-8 lg:border-l lg:border-t-0">
                        <div class="grid grid-cols-2 gap-3">
                            <a href="#vocabulary" class="rounded-xl bg-white/10 p-4 hover:bg-white/15">
                                <p class="text-2xl font-black">{{ count($topic['vocabulary']) }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-300">Từ vựng</p>
                            </a>
                            <a href="#patterns" class="rounded-xl bg-white/10 p-4 hover:bg-white/15">
                                <p class="text-2xl font-black">{{ count($topic['patterns']) }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-300">Mẫu câu</p>
                            </a>
                            <a href="#dialogue" class="rounded-xl bg-white/10 p-4 hover:bg-white/15">
                                <p class="text-2xl font-black">{{ count($topic['dialogue']) }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-300">Hội thoại</p>
                            </a>
                            <a href="#quiz" class="rounded-xl bg-white/10 p-4 hover:bg-white/15">
                                <p class="text-2xl font-black">{{ count($topic['quiz']) }}</p>
                                <p class="mt-1 text-sm font-semibold text-slate-300">Quiz</p>
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            @if($quizResult)
                <section class="mb-8 rounded-2xl border {{ $quizResult['passed'] ? 'border-green-200 bg-green-50' : 'border-amber-200 bg-amber-50' }} p-5">
                    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                        <div>
                            <p class="text-sm font-bold {{ $quizResult['passed'] ? 'text-green-700' : 'text-amber-700' }}">
                                Kết quả quiz: {{ $quizResult['score'] }}/{{ $quizResult['total'] }} · {{ $quizResult['percent'] }}%
                            </p>
                            <p class="mt-1 text-sm {{ $quizResult['passed'] ? 'text-green-800' : 'text-amber-800' }}">
                                {{ $quizResult['passed'] ? 'Đạt. Chuyển sang flashcard hoặc mini task.' : 'Chưa đạt. Xem lại câu sai rồi làm lại.' }}
                            </p>
                        </div>
                        <a href="#quiz" class="inline-flex items-center justify-center rounded-lg bg-white px-4 py-2 text-sm font-bold text-gray-800 hover:bg-gray-50">
                            Xem câu quiz
                        </a>
                    </div>
                </section>
            @endif

            <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
                <section id="vocabulary" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="mb-4">
                        <h2 class="text-xl font-extrabold text-gray-950">Từ vựng</h2>
                        <p class="mt-1 text-sm text-gray-600">Từ dùng trực tiếp trong tình huống này.</p>
                    </div>
                    <div class="space-y-3">
                        @foreach($topic['vocabulary'] as $item)
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-lg font-black text-gray-950">{{ $item['jp'] }} <span class="text-sm font-semibold text-gray-500">{{ $item['reading'] }}</span></p>
                                        <p class="mt-1 text-sm text-gray-700">{{ $item['meaning'] }}</p>
                                        <p class="mt-2 text-sm text-gray-500">{{ $item['example'] }}</p>
                                    </div>
                                    <button type="button" data-speak="{{ $item['jp'] }}" class="speak-button inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white text-sm font-black text-red-600 shadow-sm ring-1 ring-gray-200 hover:bg-red-50" title="Nghe">
                                        ▶
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section id="patterns" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="mb-4">
                        <h2 class="text-xl font-extrabold text-gray-950">Mẫu câu</h2>
                        <p class="mt-1 text-sm text-gray-600">Mẫu có thể thay từ để dùng trong nhiều tình huống.</p>
                    </div>
                    <div class="space-y-3">
                        @foreach($topic['patterns'] as $pattern)
                            <div class="rounded-xl border border-gray-200 p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-lg font-black text-gray-950">{{ $pattern['jp'] }}</p>
                                        <p class="mt-1 text-sm font-semibold text-red-700">{{ $pattern['meaning'] }}</p>
                                        <p class="mt-2 text-sm text-gray-600">{{ $pattern['usage'] }}</p>
                                        <p class="mt-2 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700">{{ $pattern['example'] }}</p>
                                    </div>
                                    <button type="button" data-speak="{{ $pattern['example'] }}" class="speak-button inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gray-50 text-sm font-black text-red-600 ring-1 ring-gray-200 hover:bg-red-50" title="Nghe">
                                        ▶
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <section id="dialogue" class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
                <div class="mb-4 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-950">Hội thoại</h2>
                        <p class="mt-1 text-sm text-gray-600">Nghe từng câu và đọc lại theo vai.</p>
                    </div>
                    <button type="button" data-speak="{{ collect($topic['dialogue'])->pluck('jp')->implode(' ') }}" class="speak-button inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700" title="Nghe toàn bộ">
                        ▶ Nghe toàn bộ
                    </button>
                </div>
                <div class="grid gap-3 lg:grid-cols-2">
                    @foreach($topic['dialogue'] as $line)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div class="flex items-start gap-3">
                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-950 text-sm font-black text-white">{{ $line['speaker'] }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="text-lg font-black text-gray-950">{{ $line['jp'] }}</p>
                                    <p class="mt-1 text-sm text-gray-600">{{ $line['vi'] }}</p>
                                </div>
                                <button type="button" data-speak="{{ $line['jp'] }}" class="speak-button inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white text-xs font-black text-red-600 ring-1 ring-gray-200 hover:bg-red-50" title="Nghe">
                                    ▶
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>

            <div class="mt-6 grid gap-6 xl:grid-cols-[0.9fr_1.1fr]">
                <section id="flashcards" class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="mb-4">
                        <h2 class="text-xl font-extrabold text-gray-950">Flashcard</h2>
                        <p class="mt-1 text-sm text-gray-600">Lật thẻ nhanh trước khi làm quiz.</p>
                    </div>
                    <div class="grid gap-3 sm:grid-cols-2">
                        @foreach($topic['flashcards'] as $card)
                            <button type="button" class="flashcard rounded-xl border border-gray-200 bg-gray-50 p-4 text-left hover:border-red-200 hover:bg-red-50" data-front="{{ $card['front'] }}" data-back="{{ $card['back'] }}">
                                <p class="flashcard-text min-h-12 text-lg font-black text-gray-950">{{ $card['front'] }}</p>
                                @if(!empty($card['hint']))
                                    <p class="mt-3 text-xs text-gray-500">{{ $card['hint'] }}</p>
                                @endif
                            </button>
                        @endforeach
                    </div>
                </section>

                <section id="mini-task" class="rounded-2xl border border-slate-800 bg-slate-950 p-5 text-white shadow-sm md:p-6">
                    <p class="text-sm font-bold uppercase tracking-wide text-red-200">Mini task 5 phút</p>
                    <h2 class="mt-2 text-2xl font-extrabold">{{ $topic['mini_task']['title'] }}</h2>
                    <div class="mt-5 grid gap-3 sm:grid-cols-2">
                        @foreach($topic['mini_task']['steps'] as $index => $step)
                            <div class="rounded-xl bg-white/10 p-4">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-sm font-black text-slate-950">{{ $index + 1 }}</span>
                                <p class="mt-3 text-sm font-semibold leading-6 text-slate-100">{{ $step }}</p>
                            </div>
                        @endforeach
                    </div>
                </section>
            </div>

            <section id="quiz" class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
                <div class="mb-5">
                    <h2 class="text-xl font-extrabold text-gray-950">Quiz</h2>
                    <p class="mt-1 text-sm text-gray-600">Kiểm tra nhanh phần vừa học.</p>
                </div>
                <form action="{{ $topic['quiz_url'] }}" method="POST" class="space-y-5">
                    @csrf
                    @foreach($topic['quiz'] as $question)
                        @php
                            $answerResult = collect($quizResult['answers'] ?? [])->firstWhere('id', $question['id']);
                        @endphp
                        <fieldset class="rounded-xl border border-gray-200 p-4">
                            <legend class="px-1 text-sm font-bold text-gray-950">{{ $loop->iteration }}. {{ $question['prompt'] }}</legend>
                            <div class="mt-3 grid gap-2 md:grid-cols-{{ count($question['options']) >= 4 ? '4' : '3' }}">
                                @foreach($question['options'] as $option)
                                    <label class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm font-semibold text-gray-700 hover:border-red-200 hover:bg-red-50">
                                        <input type="radio" name="answers[{{ $question['id'] }}]" value="{{ $option }}" class="text-red-600 focus:ring-red-500" {{ ($answerResult['selected'] ?? '') === $option ? 'checked' : '' }}>
                                        <span>{{ $option }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @if($answerResult)
                                <div class="mt-3 rounded-lg {{ $answerResult['correct'] ? 'bg-green-50 text-green-800' : 'bg-red-50 text-red-800' }} px-3 py-2 text-sm">
                                    <span class="font-bold">{{ $answerResult['correct'] ? 'Đúng' : 'Sai' }}.</span>
                                    Đáp án: {{ $answerResult['answer'] }}.
                                    @if(!empty($answerResult['explanation']))
                                        {{ $answerResult['explanation'] }}
                                    @endif
                                </div>
                            @endif
                        </fieldset>
                    @endforeach
                    <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-3 text-sm font-bold text-white hover:bg-red-700">
                        Nộp quiz
                    </button>
                </form>
            </section>
        </div>
    </main>

    <script>
        const pronunciationUrl = @json(route('pronunciation.resolve'));

        function speakWithBrowser(text) {
            if (!('speechSynthesis' in window)) {
                return;
            }

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'ja-JP';
            utterance.rate = 0.9;
            speechSynthesis.cancel();
            speechSynthesis.speak(utterance);
        }

        async function speakJapanese(text) {
            if (!text) {
                return;
            }

            try {
                const url = `${pronunciationUrl}?text=${encodeURIComponent(text)}&language=ja-JP`;
                const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
                const payload = await response.json();
                const audioUrl = payload?.audio?.audio_url;

                if (audioUrl) {
                    const audio = new Audio(audioUrl);
                    await audio.play();
                    return;
                }
            } catch (error) {
                // Browser speech is the fallback for local/offline TTS providers.
            }

            speakWithBrowser(text);
        }

        document.querySelectorAll('.speak-button').forEach((button) => {
            button.addEventListener('click', () => speakJapanese(button.dataset.speak || ''));
        });

        document.querySelectorAll('.flashcard').forEach((button) => {
            button.addEventListener('click', () => {
                const textNode = button.querySelector('.flashcard-text');
                const current = textNode.textContent.trim();
                const front = button.dataset.front;
                const back = button.dataset.back;
                textNode.textContent = current === front ? back : front;
            });
        });
    </script>

    @include('layouts.footer')
</body>
</html>
