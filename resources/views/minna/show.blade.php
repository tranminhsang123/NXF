<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lesson->title }} - Minna no Nihongo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .japanese-text {
            font-family: 'Hiragino Sans', 'Yu Gothic', 'Meiryo', sans-serif;
            font-size: 1.2em;
        }
        .sidebar-item {
            transition: all 0.3s ease;
        }
        .sidebar-item:hover {
            transform: translateX(4px);
        }
        .sidebar-item.active {
            background: linear-gradient(90deg, #dc2626 0%, #ef4444 100%);
            color: white;
        }
        .section-content {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease-in-out;
            min-height: 200px; /* Prevent layout shift */
        }
        .section-content.active {
            display: block;
            opacity: 1;
        }
        .sticky-sidebar {
            position: sticky;
            top: 100px;
            max-height: calc(100vh - 120px);
            overflow-y: auto;
        }
        @media (max-width: 1024px) {
            .sticky-sidebar {
                position: relative;
                top: 0;
                max-height: none;
            }
        }
        .sticky-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sticky-sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .sticky-sidebar::-webkit-scrollbar-thumb {
            background: #dc2626;
            border-radius: 10px;
        }
        .sticky-sidebar::-webkit-scrollbar-thumb:hover {
            background: #b91c1c;
        }
        .hide-meaning .meaning-text {
            filter: blur(0.32rem);
            transition: filter 0.15s ease-in-out;
            user-select: none;
        }
        .hide-grammar-explain .grammar-explain {
            display: none;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100">
    @include('layouts.header')

    <div class="container mx-auto px-4 lg:px-8 py-8 md:py-16 lg:py-24">
        <!-- Breadcrumb -->
        <nav class="mb-6">
            <a href="{{ route('minna.index') }}" class="inline-flex items-center text-red-600 hover:text-red-700 transition group">
                <svg class="w-5 h-5 mr-2 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Quay lại danh sách bài học
            </a>
        </nav>

        <!-- Lesson Header Card -->
        <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 lg:p-8 mb-6 md:mb-8 border border-gray-100">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 md:gap-6 mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 md:gap-3 mb-3">
                        <span class="inline-flex items-center justify-center w-10 h-10 md:w-12 md:h-12 bg-gradient-to-br from-red-600 to-red-700 text-white text-lg md:text-xl font-bold rounded-xl shadow-lg flex-shrink-0">
                            {{ str_pad($lesson->number, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        <h1 class="text-2xl md:text-3xl lg:text-4xl font-bold text-gray-900 break-words">{{ $lesson->title }}</h1>
                    </div>
                    @if($lesson->description)
                        <p class="text-gray-600 text-sm md:text-base lg:text-lg ml-0 md:ml-14 lg:ml-16">{{ $lesson->description }}</p>
                    @endif
                </div>

                @auth
                    <div class="w-full md:w-auto">
                        @php
                            $isCompleted = isset($progress) && $progress && $progress->status === \App\Models\UserProgress::STATUS_COMPLETED;
                            $lessonPercent = $sectionSummary['percent'] ?? ($isCompleted ? 100 : 0);
                        @endphp
                        <div class="bg-gray-50 rounded-xl p-3 md:p-4 border border-gray-200">
                            <p class="text-xs md:text-sm font-semibold text-gray-700 mb-2">Tiến độ bài học</p>
                            <div class="mb-3">
                                <div class="flex items-center justify-between text-xs text-gray-500 mb-1">
                                    <span>{{ $sectionSummary['completed'] ?? 0 }}/{{ $sectionSummary['total'] ?? 0 }} phần</span>
                                    <span>{{ $lessonPercent }}%</span>
                                </div>
                                <div class="h-2 rounded-full bg-gray-200 overflow-hidden">
                                    <div class="h-2 rounded-full {{ $isCompleted ? 'bg-green-600' : 'bg-red-600' }}" style="width: {{ $lessonPercent }}%"></div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                @if($isCompleted)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Đã hoàn thành
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l2 2m6-4a8 8 0 11-16 0 8 8 0 0116 0z"></path>
                                        </svg>
                                        Đang học
                                    </span>
                                @endif

                                @if(! $isCompleted)
                                    <form method="POST" action="{{ route('minna.complete', ['number' => $lesson->number]) }}" class="ml-auto">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs md:text-sm font-semibold text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 shadow-sm hover:shadow-md transition-all">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Đánh dấu hoàn thành
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endauth
            </div>
        </div>

        @if(session('status'))
            <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm font-semibold text-green-700">
                {{ session('status') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-800">
                {{ session('warning') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-lg p-4 md:p-6 mb-6 md:mb-8 border border-gray-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-red-600 mb-1">Chế độ học nhanh</p>
                    <h2 class="text-xl font-bold text-gray-900">Luyện nhớ ngay trong bài học</h2>
                    <p class="text-sm text-gray-500 mt-1">Ẩn nghĩa để tự kiểm tra, ẩn giải thích để tự đoán ngữ pháp, hoặc nghe nhanh câu tiếng Nhật đang xem.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="global-meaning-toggle"
                            class="px-3 py-2 rounded-lg border border-gray-300 text-sm font-semibold text-gray-700 hover:border-red-400 hover:text-red-600 transition">
                        Ẩn nghĩa
                    </button>
                    <button type="button" id="global-grammar-toggle"
                            class="px-3 py-2 rounded-lg border border-gray-300 text-sm font-semibold text-gray-700 hover:border-red-400 hover:text-red-600 transition">
                        Ẩn giải thích
                    </button>
                    <button type="button" id="speak-active-section"
                            class="px-3 py-2 rounded-lg border border-gray-300 text-sm font-semibold text-gray-700 hover:border-red-400 hover:text-red-600 transition">
                        Phát âm
                    </button>
                    <a href="{{ route('flashcard.study', $lesson->number) }}"
                       class="px-3 py-2 rounded-lg bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition">
                        Ôn flashcard
                    </a>
                    <a href="{{ route('flashcard.study', ['number' => $lesson->number, 'mode' => 'srs']) }}"
                       class="px-3 py-2 rounded-lg bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition">
                        SRS bài này
                    </a>
                    @auth
                        <a href="{{ route('minna.quiz.advanced', ['number' => $lesson->number]) }}"
                           class="px-3 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition">
                            Quiz nâng cao
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <div class="flex flex-col lg:flex-row gap-4 md:gap-6 lg:gap-8">
            <!-- Sidebar Navigation -->
            <aside class="w-full lg:w-80 flex-shrink-0">
                <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 sticky-sidebar border border-gray-100">
                    <div class="flex items-center justify-between mb-4 md:mb-6 pb-3 md:pb-4 border-b-2 border-red-600">
                        <h2 class="text-lg md:text-xl font-bold text-gray-900">📚 Nội dung bài học</h2>
                        <a href="{{ route('flashcard.study', $lesson->number) }}" class="text-xs font-semibold text-red-600 hover:text-red-700 whitespace-nowrap">Ôn Flashcard</a>
                    </div>
                    <nav class="space-y-2" id="section-nav">
                        @php
                            $sectionTitles = [
                                'tu-vung' => 'Từ vựng',
                                'ngu-phap' => 'Ngữ pháp',
                                'luyen-doc' => 'Luyện đọc',
                                'hoi-thoai' => 'Hội thoại',
                                'han-tu' => 'Hán tự'
                            ];
                            $sectionIcons = [
                                'tu-vung' => '📚',
                                'ngu-phap' => '📖',
                                'luyen-doc' => '📝',
                                'hoi-thoai' => '💬',
                                'han-tu' => '✍️'
                            ];
                            $sectionOrder = ['tu-vung', 'ngu-phap', 'luyen-doc', 'hoi-thoai', 'han-tu'];
                            $orderedNavKeys = [];
                            foreach ($sectionOrder as $orderedKey) {
                                if (isset($sectionsByKey[$orderedKey])) {
                                    $orderedNavKeys[] = $orderedKey;
                                }
                            }
                            foreach ($sectionsByKey->keys() as $key) {
                                if (! in_array($key, $sectionOrder)) {
                                    $orderedNavKeys[] = $key;
                                }
                            }
                            $navFirst = true;
                        @endphp
                        @forelse($orderedNavKeys as $key)
                            @php
                                $navSectionGroup = $sectionsByKey[$key];
                                $navCompleted = $navSectionGroup->every(fn ($section) => ($sectionProgressById ?? collect())->has($section->id));
                            @endphp
                            <button 
                                onclick="showSection('{{ $key }}')"
                                class="section-nav-btn w-full sidebar-item p-3 md:p-4 rounded-xl text-left font-semibold text-sm md:text-base text-gray-700 hover:bg-red-50 transition-all {{ $navFirst ? 'active' : '' }}"
                                data-section="{{ $key }}">
                                <div class="flex items-center gap-2 md:gap-3">
                                    <span class="text-xl md:text-2xl">{{ $sectionIcons[$key] ?? '📘' }}</span>
                                    <span>{{ $sectionTitles[$key] ?? ucwords(str_replace('-', ' ', $key)) }}</span>
                                    @if($navCompleted)
                                        <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Xong</span>
                                    @endif
                                </div>
                            </button>
                            @php $navFirst = false; @endphp
                        @empty
                            <p class="text-sm text-gray-500">Chưa có nội dung cho bài này.</p>
                        @endforelse
                    </nav>

                    <!-- Progress Indicator -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        @php
                            $progressPercent = $sectionSummary['percent'] ?? 0;
                        @endphp
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-sm font-medium text-gray-600">Tiến độ học tập</span>
                            <span class="text-sm font-bold text-red-600">{{ $progressPercent }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gradient-to-r from-red-600 to-red-500 h-2 rounded-full transition-all duration-300" style="width: {{ $progressPercent }}%"></div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content Area -->
            <main class="flex-1 min-w-0">
                <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 lg:p-8 border border-gray-100" style="min-height: 400px;">
                    @php
                        $contentFirst = true;
                    @endphp
                    @forelse($orderedNavKeys as $key)
                        @php $sectionGroup = $sectionsByKey[$key]; @endphp
                        <div 
                            id="section-{{ $key }}" 
                            class="section-content {{ $contentFirst ? 'active' : '' }}"
                            data-section="{{ $key }}">
                            @php $contentFirst = false; @endphp

                            @foreach($sectionGroup as $index => $section)
                                <div class="{{ $index > 0 ? 'mt-10 pt-6 border-t border-dashed border-gray-200' : '' }}">
                                    <div class="flex items-center gap-2 md:gap-4 mb-6 md:mb-8 pb-4 md:pb-6 border-b-2 border-red-600">
                                        <span class="text-2xl md:text-3xl lg:text-4xl">
                                            {{ $sectionIcons[$key] ?? '📘' }}
                                        </span>
                                        <h2 class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-900 break-words">
                                            {{ $section->title }}
                                            @if($sectionGroup->count() > 1)
                                                <span class="text-sm md:text-base text-gray-500 font-medium ml-2">
                                                    Phần {{ $index + 1 }}
                                                </span>
                                            @endif
                                        </h2>
                                    </div>

                                    <div class="section-body">
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
                                                <div class="text-gray-700 space-y-3">
                                                    <pre class="bg-gray-50 rounded-xl p-4 overflow-auto text-sm">{{ json_encode($section->content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center py-12">
                                                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 rounded-full mb-4">
                                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </div>
                                                <p class="text-gray-500 text-lg italic">Nội dung đang được cập nhật...</p>
                                            </div>
                                        @endif
                                    </div>
                                    @auth
                                        @php $sectionDone = ($sectionProgressById ?? collect())->has($section->id); @endphp
                                        <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end">
                                            @if($sectionDone)
                                                <span class="inline-flex items-center px-4 py-2 rounded-lg bg-green-100 text-green-700 text-sm font-semibold">
                                                    Phan nay da xong
                                                </span>
                                            @else
                                                <form method="POST" action="{{ route('minna.section.complete', ['number' => $lesson->number, 'section' => $section->id]) }}">
                                                    @csrf
                                                    <button type="submit" class="px-4 py-2 rounded-lg bg-green-600 text-white text-sm font-semibold hover:bg-green-700">
                                                        Đánh dấu xong phần này
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @endauth
                                </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="text-center py-16">
                            <p class="text-gray-500 text-lg">Chưa có nội dung cho bài học này.</p>
                        </div>
                    @endforelse
                </div>

                @if(!empty($quizQuestions))
                    <div class="bg-white rounded-2xl shadow-xl p-4 md:p-6 lg:p-8 border border-gray-100 mt-6 md:mt-8">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-red-600 mb-1">Mini quiz</p>
                                <h2 class="text-xl md:text-2xl font-bold text-gray-900">Kiểm tra nhanh cuối bài</h2>
                                <p class="text-sm text-gray-500 mt-1">Đạt tối thiểu 80% để đánh dấu bài học hoàn thành.</p>
                            </div>
                        </div>
                        @auth
                            @if(($quizAttempts ?? collect())->isNotEmpty())
                                <div class="mb-6 rounded-xl border border-gray-200 bg-gray-50 p-4">
                                    <p class="text-sm font-semibold text-gray-900 mb-3">Lịch sử quiz gần đây</p>
                                    <div class="space-y-2">
                                        @foreach($quizAttempts as $attempt)
                                            <div class="flex items-center justify-between gap-3 rounded-lg bg-white border border-gray-200 px-3 py-2 text-sm">
                                                <span class="font-semibold {{ $attempt->passed ? 'text-green-700' : 'text-amber-700' }}">
                                                    {{ $attempt->score }}/{{ $attempt->total }} - {{ $attempt->percent }}%
                                                </span>
                                                <span class="text-xs text-gray-500">{{ $attempt->completed_at?->format('d/m/Y H:i') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <form method="POST" action="{{ route('minna.quiz.submit', ['number' => $lesson->number]) }}" class="space-y-5">
                                @csrf
                                @foreach($quizQuestions as $qIndex => $question)
                                    <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                                        <p class="font-semibold text-gray-900 mb-3">
                                            {{ $qIndex + 1 }}. <span class="japanese-text">{{ $question['prompt'] }}</span>
                                        </p>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                            @foreach($question['options'] as $option)
                                                <label class="flex items-start gap-2 rounded-lg bg-white border border-gray-200 p-3 cursor-pointer hover:border-red-300">
                                                    <input type="radio" name="answers[{{ $qIndex }}]" value="{{ $option }}" class="mt-1 text-red-600 focus:ring-red-500" required>
                                                    <span class="text-sm text-gray-700">{{ $option }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                                <button type="submit" class="px-5 py-3 rounded-xl bg-red-600 text-white font-semibold hover:bg-red-700">
                                    Nop quiz
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex px-4 py-2 rounded-lg bg-red-600 text-white font-semibold hover:bg-red-700">Đăng nhập để làm quiz</a>
                        @endauth
                    </div>
                @endif

                <!-- Navigation Buttons -->
                @php
                    $previousLessonUrl = isset($previousLessonNumber) ? route('minna.show', ['number' => $previousLessonNumber]) : null;
                    $nextLessonUrl = isset($nextLessonNumber) ? route('minna.show', ['number' => $nextLessonNumber]) : null;
                @endphp
                <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center mt-6 md:mt-8 gap-3 md:gap-4">
                    <button 
                        type="button"
                        data-nav="prev"
                        @if($previousLessonUrl)
                            onclick="navigateToLesson('{{ $previousLessonUrl }}')"
                        @else
                            disabled
                        @endif
                        class="flex items-center justify-center gap-2 px-4 md:px-6 py-2.5 md:py-3 rounded-xl font-semibold text-sm md:text-base transition-all border
                            {{ $previousLessonUrl ? 'bg-white shadow-lg hover:shadow-xl border-gray-200 hover:border-red-300 text-gray-700 hover:text-red-600' : 'bg-gray-100 border-gray-200 text-gray-400 cursor-not-allowed' }}">
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="hidden sm:inline">Bài trước</span>
                        <span class="sm:hidden">Trước</span>
                    </button>
                    <button 
                        type="button"
                        data-nav="next"
                        @if($nextLessonUrl)
                            onclick="navigateToLesson('{{ $nextLessonUrl }}')"
                        @else
                            disabled
                        @endif
                        class="flex items-center justify-center gap-2 px-4 md:px-6 py-2.5 md:py-3 rounded-xl font-semibold text-sm md:text-base transition-all
                            {{ $nextLessonUrl ? 'bg-gradient-to-r from-red-600 to-red-700 text-white shadow-lg hover:shadow-xl hover:from-red-700 hover:to-red-800' : 'bg-gray-200 text-gray-400 cursor-not-allowed' }}">
                        <span class="hidden sm:inline">Bài tiếp theo</span>
                        <span class="sm:hidden">Tiếp</span>
                        <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </main>
        </div>
    </div>

    @include('layouts.footer')

    <script>
        // Back to top button
        (function () {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.innerHTML = `
                <span class="sr-only">Lên đầu trang</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                </svg>
            `;
            btn.id = 'back-to-top';
            btn.className = 'fixed z-40 right-4 bottom-4 md:right-6 md:bottom-6 w-10 h-10 md:w-11 md:h-11 rounded-full bg-red-600 text-white shadow-lg flex items-center justify-center opacity-0 pointer-events-none translate-y-4 transition-all duration-300 hover:bg-red-700';

            document.body.appendChild(btn);

            const toggleVisibility = () => {
                const header = document.getElementById('main-header');
                const offset = (header ? header.offsetHeight : 80) + 200;
                const scrolledPastHeader = window.scrollY > offset;

                const nearBottom = (window.innerHeight + window.scrollY) >= (document.body.offsetHeight - 200);

                if (scrolledPastHeader && !nearBottom) {
                    btn.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-4');
                } else {
                    btn.classList.add('opacity-0', 'pointer-events-none', 'translate-y-4');
                }
            };

            window.addEventListener('scroll', toggleVisibility);

            btn.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        })();

        function showSection(sectionKey) {
            // Hide all sections with smooth fade out
            document.querySelectorAll('.section-content').forEach(section => {
                section.style.opacity = '0';
                setTimeout(() => {
                    section.classList.remove('active');
                }, 150);
            });

            // Show selected section with smooth fade in
            const targetSection = document.getElementById('section-' + sectionKey);
            if (targetSection) {
                setTimeout(() => {
                    targetSection.classList.add('active');
                    // Force reflow to ensure transition works
                    targetSection.offsetHeight;
                    targetSection.style.opacity = '1';
                    
                    // On mobile, scroll to the main content area after showing section
                    if (window.innerWidth < 1024) {
                        setTimeout(() => {
                            const mainContent = document.querySelector('main .bg-white');
                            if (mainContent) {
                                // Get header height dynamically
                                const header = document.querySelector('header');
                                const headerHeight = header ? header.offsetHeight : 80;
                                
                                // Calculate scroll position with offset for better visibility
                                const rect = mainContent.getBoundingClientRect();
                                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                                const offset = rect.top + scrollTop - headerHeight - 20; // 20px extra spacing
                                
                                window.scrollTo({
                                    top: Math.max(0, offset),
                                    behavior: 'smooth'
                                });
                            }
                        }, 200);
                    }
                    
                    // Update URL hash without scrolling (for bookmarking)
                    if (history.pushState) {
                        history.pushState(null, null, '#section-' + sectionKey);
                    }
                }, 150);
            }

            // Update active nav button
            document.querySelectorAll('.section-nav-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            const activeBtn = document.querySelector(`[data-section="${sectionKey}"]`);
            if (activeBtn) {
                activeBtn.classList.add('active');
            }
        }

        const lessonNav = {
            prevUrl: @json($previousLessonUrl),
            nextUrl: @json($nextLessonUrl),
        };

        function navigateToLesson(url) {
            if (!url) {
                return;
            }
            window.location.href = url;
        }

        const meaningToggle = document.getElementById('global-meaning-toggle');
        const grammarToggle = document.getElementById('global-grammar-toggle');
        const speakButton = document.getElementById('speak-active-section');

        meaningToggle?.addEventListener('click', function () {
            const isHidden = document.documentElement.classList.toggle('hide-meaning');
            meaningToggle.textContent = isHidden ? 'Hiện nghĩa' : 'Ẩn nghĩa';
        });

        grammarToggle?.addEventListener('click', function () {
            const isHidden = document.documentElement.classList.toggle('hide-grammar-explain');
            grammarToggle.textContent = isHidden ? 'Hiện giải thích' : 'Ẩn giải thích';
        });

        speakButton?.addEventListener('click', function () {
            if (!('speechSynthesis' in window)) {
                speakButton.textContent = 'Không hỗ trợ đọc';
                return;
            }

            const activeSection = document.querySelector('.section-content.active');
            const textNode = activeSection?.querySelector('.japanese-text');
            const text = textNode?.textContent?.trim();
            if (!text) {
                return;
            }

            const utterance = new SpeechSynthesisUtterance(text);
            utterance.lang = 'ja-JP';
            utterance.rate = 0.9;
            speechSynthesis.cancel();
            speechSynthesis.speak(utterance);
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    navigateToLesson(lessonNav.prevUrl);
                } else if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    navigateToLesson(lessonNav.nextUrl);
                }
            }
        });
        
        // Handle initial section from URL hash
        window.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash;
            if (hash && hash.startsWith('#section-')) {
                const sectionKey = hash.replace('#section-', '');
                const sectionExists = document.getElementById('section-' + sectionKey);
                if (sectionExists) {
                    showSection(sectionKey);
                }
            }
        });
    </script>
</body>
</html>
