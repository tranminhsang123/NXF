@extends('adminlayout.app')

@section('content')
@php
    $sectionTitles = [
        'tu-vung' => 'Từ vựng',
        'ngu-phap' => 'Ngữ pháp',
        'luyen-doc' => 'Luyện đọc',
        'hoi-thoai' => 'Hội thoại',
        'han-tu' => 'Hán tự',
        'quiz' => 'Quiz tự động',
        'flashcards' => 'Flashcard tự động',
    ];
@endphp

<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Xem như người học thật</h1>
        <p class="text-gray-600 mt-2">Bài {{ $lesson->number }} - {{ $lesson->title }} • {{ $lesson->publishStatusLabel() }}</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.content-studio.index', ['q' => $lesson->number]) }}" class="rounded-lg bg-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-300">Quay lại Studio</a>
        <a href="{{ route('admin.minna.show', $lesson) }}" class="rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Sửa bài</a>
    </div>
</div>

<div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-gray-500">Từ / câu học</p>
        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $diagnostics['vocab_count'] }}</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-gray-500">Audio thiếu</p>
        <p class="mt-2 text-2xl font-bold {{ $diagnostics['missing_audio_count'] > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $diagnostics['missing_audio_count'] }}</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-gray-500">Quiz có thể tạo</p>
        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $diagnostics['mini_quiz_count'] + $diagnostics['advanced_quiz_count'] }}</p>
    </div>
    <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
        <p class="text-sm text-gray-500">Flashcard</p>
        <p class="mt-2 text-2xl font-bold text-gray-900">{{ $diagnostics['flashcard_count'] }}</p>
    </div>
</div>

<div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 md:p-6">
    <div class="mx-auto max-w-6xl">
        <div class="rounded-2xl bg-white p-5 shadow-sm md:p-8">
            <div class="mb-8 flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div>
                    <div class="mb-3 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-red-600 text-lg font-bold text-white">
                        {{ str_pad($lesson->number, 2, '0', STR_PAD_LEFT) }}
                    </div>
                    <h2 class="text-3xl font-extrabold text-gray-950">{{ $lesson->title }}</h2>
                    @if($lesson->description)
                        <p class="mt-2 max-w-2xl text-gray-600">{{ $lesson->description }}</p>
                    @endif
                </div>
                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600">
                    <p class="font-bold text-gray-900">Trạng thái học giả lập</p>
                    <p class="mt-1">User sẽ thấy nội dung theo thứ tự bên dưới khi bài và section được publish.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[260px,1fr]">
                <aside class="space-y-2">
                    @foreach($orderedKeys as $key)
                        <a href="#preview-{{ $key }}" class="block rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-bold text-gray-700 hover:border-red-200 hover:bg-red-50">
                            {{ $sectionTitles[$key] ?? ucwords(str_replace('-', ' ', $key)) }}
                        </a>
                    @endforeach
                    <a href="#preview-mini-quiz" class="block rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-bold text-gray-700 hover:border-red-200 hover:bg-red-50">Mini quiz</a>
                    <a href="#preview-flashcards" class="block rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-bold text-gray-700 hover:border-red-200 hover:bg-red-50">Flashcard</a>
                </aside>

                <main class="min-w-0 space-y-6">
                    @forelse($orderedKeys as $key)
                        <section id="preview-{{ $key }}" class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6">
                            <h3 class="mb-5 text-xl font-extrabold text-gray-950">{{ $sectionTitles[$key] ?? ucwords(str_replace('-', ' ', $key)) }}</h3>
                            @foreach($sectionsByKey[$key] as $section)
                                <div class="{{ ! $loop->first ? 'mt-6 border-t border-dashed border-gray-200 pt-6' : '' }}">
                                    <div class="mb-3 flex flex-wrap items-center gap-2">
                                        <p class="font-bold text-gray-900">{{ $section->title }}</p>
                                        <span class="rounded bg-gray-100 px-2 py-1 text-xs font-semibold text-gray-600">{{ $section->publishStatusLabel() }}</span>
                                    </div>
                                    @if($section->content)
                                        @if($key === 'tu-vung')
                                            @include('minna.sections.tu-vung', ['content' => $section->content])
                                        @elseif($key === 'ngu-phap')
                                            @include('minna.sections.ngu-phap', ['content' => $section->content])
                                        @elseif($key === 'luyen-doc')
                                            @include('minna.sections.luyen-doc', ['content' => $section->content])
                                        @elseif($key === 'hoi-thoai')
                                            @include('minna.sections.hoi-thoai', ['content' => $section->content])
                                        @elseif($key === 'han-tu')
                                            @include('minna.sections.han-tu', ['content' => $section->content])
                                        @else
                                            <pre class="max-h-96 overflow-auto rounded-xl bg-gray-50 p-4 text-xs text-gray-700">{{ json_encode($section->content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        @endif
                                    @else
                                        <p class="rounded-xl bg-gray-50 p-4 text-sm text-gray-500">Phần này chưa có nội dung.</p>
                                    @endif
                                </div>
                            @endforeach
                        </section>
                    @empty
                        <section class="rounded-2xl border border-gray-200 bg-white p-8 text-center text-gray-500">Bài này chưa có section.</section>
                    @endforelse

                    <section id="preview-mini-quiz" class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6">
                        <div class="mb-5 flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-xl font-extrabold text-gray-950">Mini quiz cuối bài</h3>
                                <p class="mt-1 text-sm text-gray-500">Sinh từ dữ liệu từ vựng hiện có.</p>
                            </div>
                            <span class="rounded bg-red-50 px-2 py-1 text-xs font-bold text-red-700">{{ count($quizQuestions) }} câu</span>
                        </div>
                        @forelse($quizQuestions as $question)
                            <div class="mb-4 rounded-xl border border-gray-200 bg-gray-50 p-4 last:mb-0">
                                <p class="font-bold text-gray-900">{{ $loop->iteration }}. {{ $question['prompt'] }}</p>
                                <div class="mt-3 grid grid-cols-1 gap-2 md:grid-cols-2">
                                    @foreach($question['options'] as $option)
                                        <span class="rounded-lg border {{ $option === $question['answer'] ? 'border-green-200 bg-green-50 text-green-700' : 'border-gray-200 bg-white text-gray-700' }} px-3 py-2 text-sm">{{ $option }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <p class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800">Chưa đủ dữ liệu để sinh mini quiz.</p>
                        @endforelse
                    </section>

                    <section id="preview-flashcards" class="rounded-2xl border border-gray-200 bg-white p-5 md:p-6">
                        <div class="mb-5 flex items-start justify-between gap-3">
                            <div>
                                <h3 class="text-xl font-extrabold text-gray-950">Flashcard sinh từ bài</h3>
                                <p class="mt-1 text-sm text-gray-500">Đây là bộ thẻ user sẽ ôn khi chọn flashcard của bài này.</p>
                            </div>
                            <span class="rounded bg-violet-50 px-2 py-1 text-xs font-bold text-violet-700">{{ count($flashcards) }} thẻ</span>
                        </div>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            @forelse(array_slice($flashcards, 0, 12) as $card)
                                <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-lg font-black text-gray-950">{{ $card['front'] }}</p>
                                    <p class="mt-2 text-sm text-gray-600">{{ $card['back'] }}</p>
                                </div>
                            @empty
                                <p class="rounded-xl bg-amber-50 p-4 text-sm text-amber-800">Chưa có flashcard vì bài chưa có từ vựng đủ nghĩa.</p>
                            @endforelse
                        </div>
                    </section>
                </main>
            </div>
        </div>
    </div>
</div>
@endsection
