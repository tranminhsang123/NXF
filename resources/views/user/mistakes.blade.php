<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lỗi sai của tôi - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <main class="min-h-screen pt-24 pb-12">
        <div class="container mx-auto max-w-6xl px-4">
            <div class="mb-6 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-sm font-bold uppercase tracking-wide text-red-600">Ôn theo điểm yếu</p>
                    <h1 class="mt-2 text-3xl font-extrabold text-gray-950">Lỗi sai của tôi</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">
                        Tổng hợp từ vựng hay sai, ngữ pháp cần xem lại, câu quiz từng làm sai, bài điểm thấp và flashcard hay quên.
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ $review_plan['primary_url'] ?? route('flashcard.index') }}"
                       class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                        Ôn lại ngay
                    </a>
                    <a href="{{ route('user.dashboard') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">
                        Dashboard
                    </a>
                </div>
            </div>

            @php
                $summary = $summary ?? [];
                $statCards = [
                    ['label' => 'Từ vựng hay sai', 'value' => $summary['wrong_vocab_count'] ?? 0, 'tone' => 'text-red-600 bg-red-50'],
                    ['label' => 'Ngữ pháp cần ôn', 'value' => $summary['wrong_grammar_count'] ?? 0, 'tone' => 'text-amber-600 bg-amber-50'],
                    ['label' => 'Câu quiz sai', 'value' => $summary['wrong_quiz_count'] ?? 0, 'tone' => 'text-blue-600 bg-blue-50'],
                    ['label' => 'Bài điểm thấp', 'value' => $summary['low_lesson_count'] ?? 0, 'tone' => 'text-violet-600 bg-violet-50'],
                    ['label' => 'Flashcard hay quên', 'value' => $summary['weak_flashcard_count'] ?? 0, 'tone' => 'text-emerald-600 bg-emerald-50'],
                ];
            @endphp

            <section class="mb-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                @foreach($statCards as $card)
                    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                        <p class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ $card['label'] }}</p>
                        <p class="mt-2 inline-flex min-w-12 items-center justify-center rounded-lg px-3 py-2 text-2xl font-extrabold {{ $card['tone'] }}">
                            {{ $card['value'] }}
                        </p>
                    </div>
                @endforeach
            </section>

            <section class="mb-8 rounded-2xl border border-slate-800 bg-slate-950 p-5 text-white shadow-sm md:p-6">
                <div class="grid gap-6 lg:grid-cols-[0.8fr_1.2fr]">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-wide text-red-200">Lộ trình sửa lỗi 5 phút/ngày</p>
                        <h2 class="mt-2 text-2xl font-extrabold">Làm đúng phần yếu nhất trước</h2>
                        <p class="mt-3 text-sm leading-6 text-slate-300">
                            Mỗi lần chỉ cần làm một vòng ngắn: flashcard khó, quiz điểm thấp, rồi xem lại câu đã sai.
                        </p>
                    </div>
                    <div class="grid gap-3 md:grid-cols-2">
                        @foreach(($review_plan['steps'] ?? []) as $index => $step)
                            <a href="{{ $step['url'] ?? '#' }}" class="rounded-xl bg-white/10 p-4 hover:bg-white/15">
                                <span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white text-sm font-black text-slate-950">{{ $index + 1 }}</span>
                                <p class="mt-3 font-bold text-white">{{ $step['title'] ?? '' }}</p>
                                <p class="mt-1 text-sm leading-5 text-slate-300">{{ $step['detail'] ?? '' }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="grid gap-6 xl:grid-cols-2">
                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-extrabold text-gray-950">Từ vựng hay sai</h2>
                            <p class="mt-1 text-sm text-gray-600">Lấy từ quiz sai và flashcard có dấu hiệu hay quên.</p>
                        </div>
                        <a href="{{ route('flashcard.index') }}" class="text-sm font-bold text-red-600 hover:text-red-700">Ôn SRS</a>
                    </div>

                    @forelse($weak_vocabulary ?? [] as $item)
                        <div class="mb-3 rounded-xl border border-gray-200 bg-gray-50 p-4 last:mb-0">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="text-lg font-extrabold text-gray-950">{{ $item['front'] }}</p>
                                    <p class="mt-1 text-sm leading-5 text-gray-600">{{ $item['back'] }}</p>
                                    @if(!empty($item['latest_selected']))
                                        <p class="mt-2 text-xs text-gray-500">Lần gần nhất: {{ $item['latest_selected'] }}</p>
                                    @endif
                                </div>
                                <div class="flex shrink-0 flex-wrap gap-2 sm:justify-end">
                                    <span class="rounded-full bg-red-100 px-2.5 py-1 text-xs font-bold text-red-700">{{ $item['mistake_count'] }} lần</span>
                                    @foreach(($item['lesson_numbers'] ?? []) as $lessonNumber)
                                        <span class="rounded-full bg-white px-2.5 py-1 text-xs font-bold text-gray-600">Bài {{ $lessonNumber }}</span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                            Chưa có từ vựng sai rõ ràng. Làm quiz hoặc ôn SRS thêm để hệ thống ghi nhận.
                        </div>
                    @endforelse
                </section>

                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="mb-4">
                        <h2 class="text-xl font-extrabold text-gray-950">Ngữ pháp hay sai</h2>
                        <p class="mt-1 text-sm text-gray-600">Ưu tiên từ câu viết lại, sắp xếp câu và bài có điểm quiz thấp.</p>
                    </div>

                    @forelse($weak_grammar ?? [] as $item)
                        <a href="{{ $item['url'] ?? '#' }}" class="mb-3 block rounded-xl border border-gray-200 p-4 hover:border-amber-200 hover:bg-amber-50 last:mb-0">
                            <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-extrabold text-gray-950">{{ $item['title'] }}</p>
                                    <p class="mt-1 text-sm leading-5 text-gray-600">{{ $item['note'] }}</p>
                                </div>
                                @if(!empty($item['lesson_number']))
                                    <span class="shrink-0 rounded-full bg-amber-100 px-2.5 py-1 text-xs font-bold text-amber-700">Bài {{ $item['lesson_number'] }}</span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                            Chưa có lỗi ngữ pháp đủ rõ. Các câu sai dạng viết lại/sắp xếp sẽ xuất hiện ở đây.
                        </div>
                    @endforelse
                </section>
            </div>

            <section id="wrong-quiz" class="mt-6 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
                <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                    <div>
                        <h2 class="text-xl font-extrabold text-gray-950">Câu quiz từng làm sai</h2>
                        <p class="mt-1 text-sm text-gray-600">Xem lại đáp án đúng và câu trả lời gần nhất.</p>
                    </div>
                    <a href="{{ route('minna.index') }}" class="text-sm font-bold text-red-600 hover:text-red-700">Vào Minna</a>
                </div>

                @forelse($wrong_quiz_answers ?? [] as $item)
                    <a href="{{ $item['quiz_url'] ?? $item['lesson_url'] ?? '#' }}" class="mb-3 block rounded-xl border border-gray-200 p-4 hover:bg-gray-50 last:mb-0">
                        <div class="grid gap-3 lg:grid-cols-[1fr_0.8fr_0.8fr]">
                            <div>
                                <div class="mb-2 flex flex-wrap gap-2">
                                    <span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700">{{ $item['type'] }}</span>
                                    @if(!empty($item['lesson_number']))
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600">Bài {{ $item['lesson_number'] }}</span>
                                    @endif
                                </div>
                                <p class="font-bold text-gray-950">{{ $item['prompt'] }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Bạn trả lời</p>
                                <p class="mt-1 text-sm text-red-700">{{ $item['selected'] }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Đáp án đúng</p>
                                <p class="mt-1 text-sm font-semibold text-green-700">{{ $item['answer'] }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                        Chưa có câu quiz sai. Khi bạn làm sai quiz, hệ thống sẽ lưu vào đây để ôn lại.
                    </div>
                @endforelse
            </section>

            <div class="mt-6 grid gap-6 xl:grid-cols-2">
                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="mb-4">
                        <h2 class="text-xl font-extrabold text-gray-950">Bài có điểm thấp</h2>
                        <p class="mt-1 text-sm text-gray-600">Bài cần làm lại quiz trước khi học tiếp.</p>
                    </div>

                    @forelse($low_score_lessons ?? [] as $lesson)
                        <div class="mb-3 rounded-xl border border-gray-200 p-4 last:mb-0">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-extrabold text-gray-950">Bài {{ $lesson['lesson_number'] }} - {{ $lesson['lesson_title'] }}</p>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Gần nhất {{ $lesson['latest_percent'] }}%, tốt nhất {{ $lesson['best_percent'] }}%, {{ $lesson['attempt_count'] }} lần làm.
                                    </p>
                                </div>
                                <a href="{{ $lesson['quiz_url'] }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-bold text-white hover:bg-red-700">
                                    Làm lại quiz
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                            Chưa có bài điểm thấp.
                        </div>
                    @endforelse
                </section>

                <section class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="mb-4 flex items-center justify-between gap-3">
                        <div>
                            <h2 class="text-xl font-extrabold text-gray-950">Flashcard hay quên</h2>
                            <p class="mt-1 text-sm text-gray-600">Thẻ có lapses, điểm nhớ thấp hoặc ease factor thấp.</p>
                        </div>
                        <a href="{{ route('flashcard.index') }}" class="text-sm font-bold text-red-600 hover:text-red-700">Flashcard</a>
                    </div>

                    @forelse($weak_flashcards ?? [] as $card)
                        <a href="{{ $card['review_url'] }}" class="mb-3 block rounded-xl border border-gray-200 p-4 hover:bg-emerald-50 last:mb-0">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                <div>
                                    <p class="font-extrabold text-gray-950">{{ $card['front'] }}</p>
                                    <p class="mt-1 text-sm leading-5 text-gray-600">{{ $card['back'] }}</p>
                                    @if(!empty($card['lesson_number']))
                                        <p class="mt-2 text-xs text-gray-500">Bài {{ $card['lesson_number'] }} - {{ $card['lesson_title'] }}</p>
                                    @endif
                                </div>
                                <div class="flex shrink-0 flex-wrap gap-2 sm:justify-end">
                                    <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-bold text-emerald-700">Lapses {{ $card['lapses'] }}</span>
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-bold text-gray-600">Nhớ {{ $card['last_quality'] ?? '-' }}/5</span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-300 p-6 text-center text-sm text-gray-500">
                            Chưa có flashcard hay quên.
                        </div>
                    @endforelse
                </section>
            </div>
        </div>
    </main>

    @include('layouts.footer')
</body>
</html>
