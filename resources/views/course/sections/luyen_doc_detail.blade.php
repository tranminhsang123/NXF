<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $courseData['title'] }} - {{ $item->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .japanese-text {
            font-family: 'Hiragino Sans', 'Yu Gothic', 'Meiryo', sans-serif;
            line-height: 1.8;
            word-break: normal;
            overflow-wrap: break-word;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    @include('layouts.header')

    <main>
        <section class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <a href="{{ route('course.section', ['level' => $level, 'sectionType' => 'luyen_doc']) }}" class="inline-flex items-center text-sm font-bold text-slate-600 hover:text-slate-950">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Luyện đọc
                </a>
                <p class="mt-5 text-sm font-bold text-red-600">{{ $item->bai }}</p>
                <h1 class="mt-1 break-words text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">{{ $item->title }}</h1>
            </div>
        </section>

        <section class="py-6 sm:py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                @php $content = $item->content; @endphp

                @if(isset($content['passage']))
                    <div class="mb-5 rounded-xl border border-blue-100 bg-blue-50 p-4 sm:p-6">
                        <h2 class="text-sm font-black uppercase tracking-wide text-slate-700">Đoạn văn</h2>
                        <p class="japanese-text mt-4 whitespace-pre-wrap text-base text-slate-800 sm:text-lg">{{ $content['passage'] }}</p>
                    </div>
                @endif

                @if(isset($content['questions']) && is_array($content['questions']))
                    <div class="space-y-4">
                        @foreach($content['questions'] as $questionIndex => $question)
                            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                                <h2 class="break-words text-base font-black text-slate-950 sm:text-lg">
                                    <span class="text-red-600">{{ $question['question_number'] ?? 'Câu ' . ($questionIndex + 1) }}</span>
                                    {{ $question['question'] ?? '' }}
                                </h2>

                                @if(isset($question['options']) && is_array($question['options']))
                                    <div class="mt-4 space-y-3">
                                        @foreach($question['options'] as $index => $option)
                                            <div class="rounded-lg border p-4 {{ isset($question['correct_answer']) && $question['correct_answer'] == $index ? 'border-green-300 bg-green-50' : 'border-slate-200 bg-slate-50' }}">
                                                <div class="flex items-start gap-3">
                                                    <span class="mt-0.5 flex h-7 w-7 shrink-0 items-center justify-center rounded-md bg-white text-sm font-black text-slate-700">{{ chr(65 + $index) }}</span>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="japanese-text break-words text-base text-slate-900 sm:text-lg">{{ $option['text'] ?? '' }}</div>
                                                        @if(isset($option['romaji']))
                                                            <div class="mt-1 text-sm italic text-slate-500">({{ $option['romaji'] }})</div>
                                                        @endif
                                                        @if(isset($option['meaning']))
                                                            <div class="mt-1 text-sm font-semibold text-slate-700">{{ $option['meaning'] }}</div>
                                                        @endif
                                                    </div>
                                                    @if(isset($question['correct_answer']) && $question['correct_answer'] == $index)
                                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-green-600 text-white" aria-label="Đáp án đúng">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if(isset($question['explanation']))
                                    <div class="mt-4 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                                        <h3 class="font-black text-slate-950">Giải thích</h3>
                                        <p class="mt-2 text-sm leading-6 text-slate-700 sm:text-base">{{ $question['explanation'] }}</p>
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    </main>

    @include('layouts.footer')
</body>
</html>
