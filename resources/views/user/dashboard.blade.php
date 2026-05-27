<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="pt-24 pb-12 min-h-screen">
        <div class="container mx-auto px-4 max-w-7xl">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Xin chào, {{ $user->name }}!</h1>
                <p class="text-gray-600">Chào mừng bạn đến với dashboard của bạn</p>
            </div>

            @php
                $plan = $learningPlan ?? [];
                $planTasks = $plan['tasks'] ?? [];
                $planSrs = $plan['srs'] ?? ['due_count' => 0, 'new_count' => 0, 'total_in_scope' => 0];
                $planLesson = $plan['resume_lesson'] ?? $plan['next_lesson'] ?? null;
                $daily = $plan['daily_goal'] ?? [];
                $gm = $gamification ?? ['xp_total' => 0, 'level' => 1, 'badges' => []];
                $road = $roadmap ?? [];
                $onboardingSummary = $onboarding ?? ($road['onboarding'] ?? []);
                $recommendedSection = $road['next_section'] ?? null;
                $advanced = $advancedDashboard ?? [];
                $dayChart = $advanced['charts']['lessons_by_day'] ?? ['labels' => [], 'data' => []];
                $weekChart = $advanced['charts']['lessons_by_week'] ?? ['labels' => [], 'data' => []];
                $dayValues = $dayChart['data'] ?? [];
                $weekValues = $weekChart['data'] ?? [];
                $maxDayValue = max(1, empty($dayValues) ? 0 : max($dayValues));
                $maxWeekValue = max(1, empty($weekValues) ? 0 : max($weekValues));
                $forecast = $advanced['forecast'] ?? [];
                $todayFocus = $todayFocus ?? [];
                $reasonFocus = $reasonFocus ?? [];
                $weakSuggestions = $weakSuggestions ?? [];
                $reasonVocab = $reasonFocus['vocabulary'] ?? [];
                $weakQuizLessons = $weakSuggestions['weak_quiz'] ?? [];
                $behaviorProfile = $weakSuggestions['behavior_profile'] ?? [];
                $weekly = $weeklyGoal ?? [];
                $weeklyMetrics = $weekly['metrics'] ?? [];
                $weeklySummary = $weekly['summary'] ?? [];
                $nextWeekPlan = $weekly['next_week_plan'] ?? [];
            @endphp

            @if($streakAtRisk ?? false)
                <div class="mb-6 rounded-xl border border-orange-400 bg-orange-50 px-4 py-3 text-sm text-orange-900">
                    <strong>Chuỗi ngày học đang “treo”:</strong> hôm qua bạn đã học — hãy ôn thẻ hoặc làm bài Minna hôm nay để không đứt chuỗi {{ $currentStreak ?? 0 }} ngày.
                </div>
            @endif

            @if (session('status'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                    {{ session('status') }}
                </div>
            @endif

            @if(empty($onboardingSummary['completed']))
                <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 px-4 py-4 text-sm text-blue-900">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <p class="font-bold">Hoàn thiện lộ trình cá nhân</p>
                            <p class="mt-1 text-blue-800">Cập nhật trình độ, mục tiêu JLPT và thời gian rảnh mỗi ngày.</p>
                        </div>
                        <a href="{{ route('onboarding.edit') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700">
                            Thiết lập ngay
                        </a>
                    </div>
                </div>
            @endif

            @if(!empty($todayFocus))
                <div class="mb-8 overflow-hidden rounded-2xl border border-slate-800 bg-slate-950 text-white shadow-sm">
                    <div class="grid gap-0 lg:grid-cols-[1.15fr_0.85fr]">
                        <div class="p-6 md:p-8">
                            <p class="text-xs font-bold uppercase tracking-wide text-red-200">Hôm nay học gì?</p>
                            @if(!empty($todayFocus['badge']))
                                <span class="mt-3 inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-red-100">{{ $todayFocus['badge'] }}</span>
                            @endif
                            <h2 class="mt-3 text-3xl font-extrabold tracking-tight">{{ $todayFocus['title'] ?? 'Tiếp tục lộ trình cá nhân' }}</h2>
                            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">{{ $todayFocus['subtitle'] ?? '' }}</p>
                            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                                <a href="{{ $todayFocus['primary_url'] ?? route('minna.index') }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-3 text-sm font-bold text-white hover:bg-red-700">
                                    {{ $todayFocus['primary_label'] ?? 'Học ngay' }}
                                </a>
                                <a href="{{ $todayFocus['secondary_url'] ?? route('flashcard.index') }}" class="inline-flex items-center justify-center rounded-lg bg-white/10 px-5 py-3 text-sm font-bold text-white hover:bg-white/15">
                                    {{ $todayFocus['secondary_label'] ?? 'Ôn SRS' }}
                                </a>
                            </div>
                        </div>
                        <div class="border-t border-white/10 bg-white/[0.04] p-6 lg:border-l lg:border-t-0 md:p-8">
                            <p class="text-sm font-bold text-slate-200">Làm trong 5 phút</p>
                            <div class="mt-4 space-y-3">
                                @foreach(($todayFocus['steps'] ?? []) as $index => $step)
                                    <div class="flex items-start gap-3 rounded-xl bg-white/10 p-3">
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-sm font-extrabold text-slate-950">{{ $index + 1 }}</span>
                                        <span class="text-sm font-semibold text-slate-100">{{ $step }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(!empty($weekly))
                <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-red-600">Mục tiêu tuần</p>
                            <h2 class="mt-2 text-2xl font-extrabold text-gray-950">Tuần {{ $weekly['week_label'] ?? '' }}</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">
                                {{ $weeklySummary['message'] ?? 'Theo dõi bài học, flashcard, quiz và số ngày giữ streak trong tuần.' }}
                            </p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse(($weeklySummary['wins'] ?? []) as $win)
                                    <span class="rounded-full bg-green-50 px-3 py-1 text-xs font-bold text-green-700">Đã xong {{ $win }}</span>
                                @empty
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-bold text-amber-700">
                                        Ưu tiên: {{ $weeklySummary['focus_label'] ?? 'Học bài' }}
                                    </span>
                                @endforelse
                            </div>
                        </div>
                        <div class="w-full lg:max-w-xs">
                            <div class="rounded-2xl bg-slate-950 p-5 text-white">
                                <div class="flex items-end justify-between">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-300">% hoàn thành tuần</p>
                                        <p class="mt-1 text-4xl font-black">{{ $weekly['percent'] ?? 0 }}%</p>
                                    </div>
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-red-100">
                                        {{ $weeklySummary['title'] ?? 'Tổng kết tuần' }}
                                    </span>
                                </div>
                                <div class="mt-4 h-3 overflow-hidden rounded-full bg-white/10">
                                    <div class="h-3 rounded-full bg-red-500" style="width: {{ min(100, (int) ($weekly['percent'] ?? 0)) }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 grid gap-3 md:grid-cols-4">
                        @foreach($weeklyMetrics as $metric)
                            <a href="{{ $metric['url'] ?? '#' }}" class="rounded-xl border border-gray-200 bg-gray-50 p-4 hover:border-red-200 hover:bg-red-50">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-bold text-gray-950">{{ $metric['label'] }}</p>
                                    <span class="rounded-full {{ !empty($metric['done']) ? 'bg-green-100 text-green-700' : 'bg-white text-gray-600' }} px-2.5 py-1 text-xs font-bold">
                                        {{ $metric['percent'] }}%
                                    </span>
                                </div>
                                <p class="mt-3 text-2xl font-black text-gray-950">
                                    {{ $metric['completed'] }}<span class="text-sm font-bold text-gray-500">/{{ $metric['target'] }} {{ $metric['unit'] }}</span>
                                </p>
                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-white">
                                    <div class="h-2 rounded-full {{ !empty($metric['done']) ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min(100, (int) $metric['percent']) }}%"></div>
                                </div>
                                @if(($metric['remaining'] ?? 0) > 0)
                                    <p class="mt-2 text-xs font-semibold text-gray-500">Còn {{ $metric['remaining'] }} {{ $metric['unit'] }}</p>
                                @else
                                    <p class="mt-2 text-xs font-semibold text-green-700">Đã đạt mục tiêu</p>
                                @endif
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-6 grid gap-4 lg:grid-cols-[0.85fr_1.15fr]">
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <p class="text-sm font-extrabold text-gray-950">Tổng kết cuối tuần</p>
                            <p class="mt-2 text-sm leading-6 text-gray-600">
                                Tập trung tiếp vào {{ $weeklySummary['focus_label'] ?? 'Học bài' }}
                                @if(($weeklySummary['focus_remaining'] ?? 0) > 0)
                                    còn {{ $weeklySummary['focus_remaining'] }} {{ $weeklySummary['focus_unit'] ?? '' }}.
                                @else
                                    để giữ nhịp ổn định.
                                @endif
                            </p>
                            <p class="mt-2 text-xs font-semibold text-gray-500">
                                Ngày đã học: {{ count($weekly['study_days'] ?? []) }}/7
                            </p>
                        </div>
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4">
                            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-extrabold text-gray-950">Gợi ý kế hoạch tuần sau</p>
                                    <p class="mt-1 text-sm text-gray-600">Dựa trên phần còn thiếu của tuần này.</p>
                                </div>
                                <a href="{{ $nextWeekPlan['primary_url'] ?? route('minna.index') }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">
                                    Bắt đầu kế hoạch
                                </a>
                            </div>
                            <div class="mt-4 grid gap-2 md:grid-cols-2">
                                @foreach(($nextWeekPlan['focus'] ?? []) as $item)
                                    <div class="rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-700">{{ $item }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if(!empty($mistakeSummary ?? []))
                <div class="mb-8 rounded-2xl border border-red-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-red-600">Lỗi sai của tôi</p>
                            <h2 class="mt-2 text-2xl font-extrabold text-gray-950">Ôn đúng phần đang yếu</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">
                                Từ vựng sai, ngữ pháp hay nhầm, bài điểm thấp và flashcard hay quên được gom vào một lộ trình sửa lỗi 5 phút/ngày.
                            </p>
                        </div>
                        <div class="grid grid-cols-2 gap-3 sm:grid-cols-5 lg:min-w-[520px]">
                            <div class="rounded-xl bg-red-50 p-3 text-center">
                                <p class="text-xl font-black text-red-700">{{ $mistakeSummary['wrong_vocab_count'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-red-900">Từ sai</p>
                            </div>
                            <div class="rounded-xl bg-amber-50 p-3 text-center">
                                <p class="text-xl font-black text-amber-700">{{ $mistakeSummary['wrong_grammar_count'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-amber-900">Ngữ pháp</p>
                            </div>
                            <div class="rounded-xl bg-blue-50 p-3 text-center">
                                <p class="text-xl font-black text-blue-700">{{ $mistakeSummary['wrong_quiz_count'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-blue-900">Quiz sai</p>
                            </div>
                            <div class="rounded-xl bg-violet-50 p-3 text-center">
                                <p class="text-xl font-black text-violet-700">{{ $mistakeSummary['low_lesson_count'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-violet-900">Bài yếu</p>
                            </div>
                            <div class="rounded-xl bg-emerald-50 p-3 text-center">
                                <p class="text-xl font-black text-emerald-700">{{ $mistakeSummary['weak_flashcard_count'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-emerald-900">Thẻ quên</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('user.mistakes') }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                            Mở lộ trình sửa lỗi
                        </a>
                        <a href="{{ $mistakeSummary['review_url'] ?? route('flashcard.index') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">
                            Ôn lại ngay
                        </a>
                    </div>
                </div>
            @endif

            @if(!empty($practicalTopicSummary ?? []))
                <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
                    <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-red-600">Chủ đề thực tế</p>
                            <h2 class="mt-2 text-2xl font-extrabold text-gray-950">Học tiếng Nhật theo tình huống</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-600">
                                Gọi món, hỏi đường, check-in khách sạn, công việc, anime, du học và hội thoại đời sống với quiz, flashcard, audio và mini task 5 phút.
                            </p>
                        </div>
                        <div class="grid grid-cols-3 gap-3 lg:min-w-[360px]">
                            <div class="rounded-xl bg-gray-50 p-3 text-center">
                                <p class="text-xl font-black text-gray-950">{{ $practicalTopicSummary['total_topics'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-gray-600">Chủ đề</p>
                            </div>
                            <div class="rounded-xl bg-red-50 p-3 text-center">
                                <p class="text-xl font-black text-red-700">{{ $practicalTopicSummary['total_vocabulary'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-red-900">Từ vựng</p>
                            </div>
                            <div class="rounded-xl bg-blue-50 p-3 text-center">
                                <p class="text-xl font-black text-blue-700">{{ $practicalTopicSummary['total_dialogues'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-blue-900">Hội thoại</p>
                            </div>
                        </div>
                    </div>
                    @if(!empty($practicalTopicSummary['recommended']))
                        <div class="mt-5 grid gap-3 md:grid-cols-3">
                            @foreach($practicalTopicSummary['recommended'] as $topic)
                                <a href="{{ $topic['url'] }}" class="rounded-xl border border-gray-200 bg-gray-50 p-4 hover:border-red-200 hover:bg-red-50">
                                    <p class="text-xs font-bold uppercase tracking-wide text-gray-500">{{ $topic['level'] }}</p>
                                    <p class="mt-1 font-extrabold text-gray-950">{{ $topic['title'] }}</p>
                                    <p class="mt-2 text-xs leading-5 text-gray-600">{{ $topic['subtitle'] }}</p>
                                </a>
                            @endforeach
                        </div>
                    @endif
                    <div class="mt-5">
                        <a href="{{ $practicalTopicSummary['index_url'] ?? route('topics.index') }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                            Mở chủ đề thực tế
                        </a>
                    </div>
                </div>
            @endif

            @if(!empty($recommendedSection))
                <div class="mb-8 rounded-2xl border border-red-200 bg-white p-6 shadow-sm">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                        <div>
                            <p class="text-xs font-semibold text-red-600 uppercase tracking-wide">Bài nên học tiếp theo</p>
                            <h2 class="mt-2 text-2xl font-bold text-gray-900">{{ $road['headline'] ?? ('Bài '.$recommendedSection['lesson_number'].' - '.$recommendedSection['section_title']) }}</h2>
                            <p class="mt-2 text-sm text-gray-600">{{ $road['reason'] ?? '' }}</p>
                            <div class="mt-3 flex flex-wrap gap-2 text-xs text-gray-600">
                                <span class="rounded-full bg-gray-100 px-3 py-1">{{ $onboardingSummary['level_label'] ?? 'Mới bắt đầu' }}</span>
                                <span class="rounded-full bg-gray-100 px-3 py-1">{{ $onboardingSummary['jlpt_goal_label'] ?? 'JLPT N5' }}</span>
                                <span class="rounded-full bg-gray-100 px-3 py-1">{{ $onboardingSummary['daily_study_minutes'] ?? 20 }} phút/ngày</span>
                                @if(!empty($onboardingSummary['placement_test_score']))
                                    <span class="rounded-full bg-blue-50 px-3 py-1 text-blue-700">Placement {{ $onboardingSummary['placement_test_score'] }}/{{ count(\App\Support\OnboardingOptions::placementQuestions()) }}</span>
                                @endif
                                @foreach(($onboardingSummary['learning_reason_labels'] ?? []) as $reasonLabel)
                                    <span class="rounded-full bg-amber-50 px-3 py-1 text-amber-700">{{ $reasonLabel }}</span>
                                @endforeach
                            </div>
                        </div>
                        <a href="{{ route('minna.section', ['number' => $recommendedSection['lesson_number'] ?? 1, 'sectionKey' => $recommendedSection['section_key'] ?? '']) }}"
                           class="inline-flex items-center justify-center rounded-lg bg-red-600 px-5 py-3 text-sm font-bold text-white hover:bg-red-700">
                            Mở bài gợi ý
                        </a>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Kinh nghiệm</p>
                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $gm['xp_total'] ?? 0 }} <span class="text-base font-medium text-gray-500">XP</span></p>
                    <p class="text-sm text-gray-600 mt-1">Cấp {{ $gm['level'] ?? 1 }}</p>
                </div>
                <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm md:col-span-2">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Huy hiệu</p>
                    @if(!empty($gm['badges']))
                        <div class="flex flex-wrap gap-2">
                            @foreach($gm['badges'] as $b)
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-50 text-red-800 px-3 py-1 text-sm border border-red-100" title="{{ $b['slug'] ?? '' }}">
                                    <span>{{ $b['icon'] ?? '🏅' }}</span>{{ $b['name'] ?? '' }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Học và ôn SRS để mở huy hiệu đầu tiên.</p>
                    @endif
                </div>
            </div>

            @if(!empty($road['next_section']) || !empty($road['weak_vocab']))
                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm mb-8">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Lộ trình gợi ý (cá nhân)</h2>
                    <p class="text-sm text-gray-600 mb-4">{{ $road['kanji_tip'] ?? '' }}</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if(!empty($road['next_section']))
                            @php $ns = $road['next_section']; @endphp
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Bước tiếp theo</p>
                                <p class="font-semibold text-gray-900">Bài {{ $ns['lesson_number'] ?? '' }} — {{ $ns['section_title'] ?? '' }}</p>
                                <a href="{{ route('minna.section', ['number' => $ns['lesson_number'] ?? 1, 'sectionKey' => $ns['section_key'] ?? '']) }}"
                                   class="mt-2 inline-flex text-sm font-semibold text-red-600 hover:text-red-700">Mở phần học →</a>
                            </div>
                        @endif
                        @if(!empty($road['weak_vocab']))
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-1">Từ vựng cần củng cố</p>
                                <ul class="text-sm text-gray-800 space-y-1">
                                    @foreach(array_slice($road['weak_vocab'], 0, 5) as $w)
                                        <li><span class="font-medium">{{ $w['front'] ?? '' }}</span> <span class="text-gray-500">(bài {{ $w['lesson_number'] ?? '?' }})</span></li>
                                    @endforeach
                                </ul>
                                <a href="{{ route('flashcard.index') }}" class="mt-2 inline-flex text-sm font-semibold text-red-600 hover:text-red-700">Vào ôn SRS →</a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            @if(!empty($reasonVocab) || !empty($weakQuizLessons) || !empty($behaviorProfile))
                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
                    @if(!empty($reasonVocab))
                        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                            <p class="text-xs font-semibold text-red-600 uppercase tracking-wide">Cá nhân hóa theo lý do học</p>
                            <h2 class="mt-2 text-xl font-bold text-gray-900">Từ vựng theo mục tiêu</h2>
                            <p class="mt-2 text-sm text-gray-600">{{ $reasonFocus['focus_text'] ?? '' }}</p>
                            <div class="mt-4 space-y-3">
                                @foreach(array_slice($reasonVocab, 0, 5) as $word)
                                    <div class="rounded-xl bg-gray-50 p-3">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $word['jp'] }} <span class="text-sm font-medium text-gray-500">{{ $word['reading'] }}</span></p>
                                                <p class="text-sm text-gray-600">{{ $word['meaning'] }}</p>
                                            </div>
                                            <span class="rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700">{{ $word['tag'] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-wide">AI gợi ý bài yếu</p>
                        <h2 class="mt-2 text-xl font-bold text-gray-900">Ôn theo lỗi quiz</h2>
                        @if(!empty($weakQuizLessons))
                            <div class="mt-4 space-y-3">
                                @foreach($weakQuizLessons as $lesson)
                                    <a href="{{ $lesson['url'] }}" class="block rounded-xl border border-gray-200 bg-gray-50 p-3 hover:border-blue-200 hover:bg-blue-50">
                                        <p class="font-bold text-gray-900">Bài {{ $lesson['lesson_number'] }} - {{ $lesson['lesson_title'] }}</p>
                                        <p class="mt-1 text-sm text-gray-600">Quiz gần đây: {{ $lesson['percent'] }}%. Nên ôn trước khi học bài mới.</p>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-4 rounded-xl bg-gray-50 p-4 text-sm text-gray-600">Chưa có bài quiz yếu. Khi có điểm thấp, hệ thống sẽ tự đưa vào lộ trình ôn.</p>
                        @endif
                    </div>

                    @if(!empty($behaviorProfile))
                        <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                            <p class="text-xs font-semibold text-emerald-600 uppercase tracking-wide">Cá nhân hóa sâu</p>
                            <h2 class="mt-2 text-xl font-bold text-gray-900">{{ $behaviorProfile['title'] ?? 'Hồ sơ học tập' }}</h2>
                            <p class="mt-2 text-sm text-gray-600">{{ $behaviorProfile['summary'] ?? '' }}</p>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @forelse(($behaviorProfile['tags'] ?? []) as $tag)
                                    <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $tag }}</span>
                                @empty
                                    <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">Tiếp tục theo lộ trình hiện tại</span>
                                @endforelse
                            </div>
                            <div class="mt-5 grid grid-cols-2 gap-3 text-center">
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-xs text-gray-500">Bài hoàn thành</p>
                                    <p class="mt-1 text-lg font-bold text-gray-900">{{ $behaviorProfile['completed_lessons'] ?? 0 }}</p>
                                </div>
                                <div class="rounded-lg bg-gray-50 p-3">
                                    <p class="text-xs text-gray-500">Điểm quiz TB</p>
                                    <p class="mt-1 text-lg font-bold text-gray-900">{{ $behaviorProfile['avg_quiz_percent'] ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <div class="lg:col-span-2 bg-gradient-to-br from-red-600 to-red-700 rounded-2xl p-6 text-white shadow-lg">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <p class="text-sm font-semibold text-red-100 mb-2">Kế hoạch học hôm nay</p>
                            <h2 class="text-2xl md:text-3xl font-bold">
                                @if($planLesson)
                                    Bài {{ $planLesson['number'] }} - {{ $planLesson['title'] }}
                                @else
                                    Bắt đầu lộ trình Minna
                                @endif
                            </h2>
                            <p class="text-red-100 mt-2">
                                SRS đến hạn {{ $planSrs['due_count'] ?? 0 }} thẻ, thẻ mới {{ $planSrs['new_count'] ?? 0 }}.
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            @if($planLesson)
                                <a href="{{ route('minna.show', ['number' => $planLesson['number']]) }}"
                                   class="inline-flex items-center justify-center px-4 py-2 bg-white text-red-700 rounded-lg text-sm font-bold hover:bg-red-50 transition">
                                    Học tiếp
                                </a>
                            @endif
                            <a href="{{ route('flashcard.index') }}"
                               class="inline-flex items-center justify-center px-4 py-2 bg-red-900/30 text-white rounded-lg text-sm font-bold hover:bg-red-900/40 transition">
                                Ôn SRS
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm">
                    <p class="text-sm text-gray-500 mb-2">Mục tiêu ngày</p>
                    <p class="text-xs text-gray-600 mb-1">Minna: <span class="font-bold text-gray-900">{{ min($todayCompletedMinnaLessons ?? 0, $dailyGoalTargetMinna ?? $dailyGoalTarget ?? 1) }}</span> / {{ $dailyGoalTargetMinna ?? $dailyGoalTarget ?? 1 }} bài</p>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-3">
                        <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $minnaDailyPercent ?? $dailyGoalPercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-600 mb-1">SRS: <span class="font-bold text-gray-900">{{ $todayFlashcardReviews ?? 0 }}</span> / {{ $dailyGoalTargetFlash ?? 12 }} thẻ đã ôn</p>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mb-2">
                        <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $flashDailyPercent ?? 0 }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500">Đạt một trong hai mục tiêu là hoàn thành ngày.</p>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-3">
                        <div class="{{ $isDailyGoalCompleted ? 'bg-green-600' : 'bg-amber-500' }} h-2 rounded-full"
                             style="width: {{ $dailyGoalPercent }}%"></div>
                    </div>
                    <p class="text-xs mt-3 {{ $isDailyGoalCompleted ? 'text-green-600' : 'text-gray-500' }}">
                        {{ $isDailyGoalCompleted ? 'Đã đạt mục tiêu hôm nay (Minna hoặc SRS).' : 'Còn Minna hoặc SRS để giữ nhịp học.' }}
                    </p>
                </div>
            </div>

            @if(!empty($planTasks))
                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm mb-8">
                    <div class="flex items-center justify-between gap-4 mb-5">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Việc nên làm tiếp theo</h2>
                            <p class="text-sm text-gray-500 mt-1">Được gợi ý theo tiến độ Minna và lịch ôn SRS của bạn.</p>
                        </div>
                        <a href="{{ route('user.progress') }}" class="text-sm font-semibold text-red-600 hover:text-red-700">Xem tiến độ</a>
                        <a href="{{ route('user.activity') }}" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Lịch sử</a>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($planTasks as $task)
                            @php
                                $target = $task['target'] ?? [];
                                $taskUrl = route('user.dashboard');
                                if (($target['screen'] ?? null) === 'LessonDetail' && !empty($target['lesson_number'])) {
                                    $taskUrl = route('minna.show', ['number' => $target['lesson_number']]);
                                } elseif (($target['screen'] ?? null) === 'Flashcards') {
                                    $taskUrl = route('flashcard.index');
                                } elseif (($target['screen'] ?? null) === 'Progress') {
                                    $taskUrl = route('user.progress');
                                }
                            @endphp
                            <a href="{{ $taskUrl }}"
                               class="block rounded-xl border {{ !empty($task['done']) ? 'border-green-200 bg-green-50' : 'border-gray-200 bg-gray-50 hover:bg-red-50 hover:border-red-200' }} p-4 transition">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex w-7 h-7 items-center justify-center rounded-full {{ !empty($task['done']) ? 'bg-green-600 text-white' : 'bg-red-600 text-white' }} text-sm font-bold">
                                        {{ !empty($task['done']) ? '✓' : '!' }}
                                    </span>
                                    <div>
                                        <p class="font-bold text-gray-900">{{ $task['title'] ?? 'Nhiệm vụ học tập' }}</p>
                                        @if(!empty($task['subtitle']))
                                            <p class="text-sm text-gray-500 mt-1">{{ $task['subtitle'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
                <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-6">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Biểu đồ tiến độ</h2>
                            <p class="text-sm text-gray-500 mt-1">Theo dõi số bài Minna đã hoàn thành gần đây.</p>
                        </div>
                        <a href="{{ route('user.statistics') }}" class="text-sm font-semibold text-red-600 hover:text-red-700">Xem thống kê đầy đủ</a>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-700">14 ngày gần nhất</p>
                                <p class="text-xs text-gray-500">Bài/ngày</p>
                            </div>
                            <div class="h-40 flex items-end gap-1 rounded-xl bg-gray-50 px-3 py-4">
                                @foreach(($dayChart['labels'] ?? []) as $index => $label)
                                    @php
                                        $value = (int) ($dayValues[$index] ?? 0);
                                        $height = $value > 0 ? max(12, (int) round(($value / $maxDayValue) * 100)) : 4;
                                    @endphp
                                    <div class="flex min-w-0 flex-1 flex-col items-center justify-end gap-2">
                                        <div class="w-full max-w-6 rounded-t bg-red-500" style="height: {{ $height }}%" title="{{ $label }}: {{ $value }} bài"></div>
                                        <span class="text-[10px] text-gray-400 {{ $index % 2 === 0 ? '' : 'hidden sm:inline' }}">{{ $label }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <div class="mb-3 flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-700">8 tuần gần nhất</p>
                                <p class="text-xs text-gray-500">Bài/tuần</p>
                            </div>
                            <div class="space-y-3">
                                @foreach(($weekChart['labels'] ?? []) as $index => $label)
                                    @php
                                        $value = (int) ($weekValues[$index] ?? 0);
                                        $width = $value > 0 ? max(6, (int) round(($value / $maxWeekValue) * 100)) : 0;
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <span class="w-24 shrink-0 text-xs text-gray-500">{{ $label }}</span>
                                        <div class="h-2.5 flex-1 rounded-full bg-gray-100">
                                            <div class="h-2.5 rounded-full bg-blue-600" style="width: {{ $width }}%" title="{{ $label }}: {{ $value }} bài"></div>
                                        </div>
                                        <span class="w-8 text-right text-xs font-semibold text-gray-700">{{ $value }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <p class="text-sm font-semibold text-gray-500">Dự báo hoàn thành</p>
                    <div class="mt-4 rounded-xl bg-emerald-50 p-4">
                        <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700">Mốc dự kiến</p>
                        <p class="mt-1 text-2xl font-bold text-gray-900">
                            @if(!empty($forecast['estimated_completion_date']))
                                {{ \Carbon\Carbon::parse($forecast['estimated_completion_date'])->format('d/m/Y') }}
                            @elseif(($forecast['remaining_lessons'] ?? 0) === 0 && ($totalMinnaLessons ?? 0) > 0)
                                Hoàn thành
                            @else
                                Chưa đủ dữ liệu
                            @endif
                        </p>
                        <p class="mt-2 text-sm text-emerald-800">{{ $forecast['message'] ?? '' }}</p>
                    </div>

                    <div class="mt-5 grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Còn lại</p>
                            <p class="mt-1 text-lg font-bold text-gray-900">{{ $forecast['remaining_lessons'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Bài/tuần</p>
                            <p class="mt-1 text-lg font-bold text-gray-900">{{ $forecast['avg_lessons_per_week'] ?? 0 }}</p>
                        </div>
                        <div class="rounded-lg bg-gray-50 p-3">
                            <p class="text-xs text-gray-500">Mục tiêu</p>
                            <p class="mt-1 text-lg font-bold text-gray-900">{{ $advanced['daily_goal_lessons'] ?? 1 }}/ngày</p>
                        </div>
                    </div>
                </div>
            </div>

            @if(!$isDailyGoalCompleted)
                <div class="mb-8 rounded-lg border border-amber-300 bg-amber-50 p-4 md:p-5">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-amber-800">Nhắc học hôm nay</p>
                            <p class="text-sm text-amber-700 mt-1">
                                Còn {{ $remainingDailyLessons }} bài Minna hoặc {{ $remainingDailyFlashcards ?? 0 }} thẻ SRS để đủ mục tiêu (chỉ cần một trong hai).
                            </p>
                        </div>
                        <a href="{{ route('leaderboard.index') }}" class="block w-full bg-slate-800 hover:bg-slate-700 text-white text-center py-2.5 rounded transition">
                            Bảng xếp hạng
                        </a>
                        <a href="{{ route('study-room.index') }}" class="block w-full bg-purple-700 hover:bg-purple-800 text-white text-center py-2.5 rounded transition">
                            Phòng học nhóm
                        </a>
                        @if($resumeMinnaLesson)
                            <a href="{{ route('minna.show', ['number' => $resumeMinnaLesson->number]) }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-md transition">
                                Học ngay bài {{ $resumeMinnaLesson->number }}
                            </a>
                        @elseif($firstMinnaLesson)
                            <a href="{{ route('minna.show', ['number' => $firstMinnaLesson->number]) }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-md transition">
                                Bắt đầu học ngay
                            </a>
                        @else
                            <a href="{{ route('minna.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold rounded-md transition">
                                Vào khu vực Minna
                            </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="mb-8 rounded-lg border border-green-300 bg-green-50 p-4 md:p-5">
                    <p class="text-sm font-semibold text-green-700">
                        Hoàn thành mục tiêu hôm nay. Tiếp tục giữ streak nhé!
                    </p>
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Bài học đã học</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $completedMinnaLessons ?? 0 }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Tổng số Kanji</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $totalKanjis ?? 0 }}
                            </p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 mb-1">Ngày học liên tiếp</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $currentStreak ?? 0 }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">Kỷ lục: {{ $longestStreak ?? 0 }} ngày</p>
                        </div>
                        <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white rounded-lg p-6 border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Hành động nhanh</h2>
                    <div class="space-y-3">
                        <a href="{{ route('alphabet.index') }}" class="block w-full bg-red-600 hover:bg-red-700 text-white text-center py-2.5 rounded transition">
                            Học bảng chữ cái
                        </a>
                        <a href="{{ route('vocabulary.index') }}" class="block w-full bg-emerald-600 hover:bg-emerald-700 text-white text-center py-2.5 rounded transition">
                            Xem từ vựng theo bài
                        </a>
                        <a href="{{ route('flashcard.index') }}" class="block w-full bg-amber-600 hover:bg-amber-700 text-white text-center py-2.5 rounded transition">
                            Ôn Flashcard
                        </a>
                        @if($resumeMinnaLesson)
                            <a href="{{ route('minna.show', ['number' => $resumeMinnaLesson->number]) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 rounded transition">
                                Học tiếp Minna - Bài {{ $resumeMinnaLesson->number }}
                            </a>
                        @elseif($firstMinnaLesson)
                            <a href="{{ route('minna.show', ['number' => $firstMinnaLesson->number]) }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 rounded transition">
                                Bắt đầu Minna - Bài {{ $firstMinnaLesson->number }}
                            </a>
                        @else
                            <a href="{{ route('minna.index') }}" class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center py-2.5 rounded transition">
                                Bài học Minna no Nihongo
                            </a>
                        @endif

                        @if($latestMinnaAccessAt)
                            <p class="text-xs text-gray-500">
                                Lần học Minna gần nhất: {{ $latestMinnaAccessAt->format('d/m/Y H:i') }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-lg p-6 border border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Tiến độ học tập</h2>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Minna no Nihongo</span>
                                <span class="text-gray-900 font-medium">
                                    {{ $minnaProgressPercent ?? 0 }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div
                                    class="bg-blue-600 h-2 rounded-full"
                                    style="width: {{ $minnaProgressPercent ?? 0 }}%"
                                ></div>
                            </div>
                        </div>
                        <div class="pt-1 flex flex-wrap gap-4">
                            <a
                                href="{{ route('user.progress') }}"
                                class="inline-flex items-center text-sm text-blue-600 hover:text-blue-700 font-medium"
                            >
                                Xem chi tiết tiến độ
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                            <a
                                href="{{ route('user.statistics') }}"
                                class="inline-flex items-center text-sm text-red-600 hover:text-red-700 font-medium"
                            >
                                Thống kê chi tiết
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </a>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between text-sm mb-1">
                                <span class="text-gray-600">Mục tiêu hôm nay</span>
                                <span class="font-medium {{ $isDailyGoalCompleted ? 'text-green-600' : 'text-gray-900' }}">
                                    Minna {{ min($todayCompletedMinnaLessons, $dailyGoalTargetMinna ?? $dailyGoalTarget) }}/{{ $dailyGoalTargetMinna ?? $dailyGoalTarget }} · SRS {{ $todayFlashcardReviews ?? 0 }}/{{ $dailyGoalTargetFlash ?? 12 }}
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div
                                    class="{{ $isDailyGoalCompleted ? 'bg-green-600' : 'bg-amber-500' }} h-2 rounded-full"
                                    style="width: {{ $dailyGoalPercent }}%"
                                ></div>
                            </div>
                            <p class="text-xs mt-2 {{ $isDailyGoalCompleted ? 'text-green-600' : 'text-gray-500' }}">
                                {{ $isDailyGoalCompleted ? 'Đã đạt ít nhất một mục tiêu trong ngày.' : 'Hoàn thành bài Minna hoặc ôn đủ thẻ SRS theo mục tiêu.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Info -->
            <div class="bg-white rounded-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 mb-1">Thông tin tài khoản</h2>
                        <p class="text-sm text-gray-600">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($streakAtRisk ?? false)
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (!('Notification' in window)) {
                    return;
                }

                var today = new Date().toISOString().slice(0, 10);
                var key = 'study-streak-reminder-' + today;
                if (window.localStorage && window.localStorage.getItem(key)) {
                    return;
                }

                function markShown() {
                    if (window.localStorage) {
                        window.localStorage.setItem(key, '1');
                    }
                }

                function showReminder() {
                    var reminderBody = @json($reasonFocus['reminder_message'] ?? 'Ôn 5 phút hôm nay để giữ streak và không quên phần vừa học.');
                    new Notification('Streak sắp đứt', {
                        body: reminderBody,
                        icon: @json(asset('images/avatar.svg')),
                    });
                    markShown();
                    return;
                    new Notification('Streak sắp đứt', {
                        body: 'Hãy ôn SRS hoặc hoàn thành một phần Minna hôm nay để giữ streak {{ $currentStreak ?? 0 }} ngày.',
                        icon: @json(asset('images/avatar.svg')),
                    });
                    markShown();
                }

                if (Notification.permission === 'granted') {
                    showReminder();
                    return;
                }

                if (Notification.permission !== 'denied') {
                    Notification.requestPermission().then(function (permission) {
                        if (permission === 'granted') {
                            showReminder();
                        } else {
                            markShown();
                        }
                    });
                }
            });
        </script>
    @endif

    @include('layouts.footer')
</body>
</html>
