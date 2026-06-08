<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ $user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .dashboard-card {
            border: 1px solid #e5e7eb;
            background: #ffffff;
            border-radius: 0.75rem;
            box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04);
        }

        .dashboard-panel summary::-webkit-details-marker {
            display: none;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    @include('layouts.header')

    <main class="min-h-screen pb-10 pt-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
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

                $minnaTarget = max(1, (int) ($dailyGoalTargetMinna ?? $dailyGoalTarget ?? 1));
                $flashTarget = max(1, (int) ($dailyGoalTargetFlash ?? 12));
                $minnaDone = min((int) ($todayCompletedMinnaLessons ?? 0), $minnaTarget);
                $flashDone = (int) ($todayFlashcardReviews ?? 0);
                $dailyDone = (bool) ($isDailyGoalCompleted ?? false);
                $minnaDailyWidth = min(100, (int) ($minnaDailyPercent ?? round(($minnaDone / $minnaTarget) * 100)));
                $flashDailyWidth = min(100, (int) ($flashDailyPercent ?? round(($flashDone / $flashTarget) * 100)));
                $dailyWidth = min(100, (int) ($dailyGoalPercent ?? max($minnaDailyWidth, $flashDailyWidth)));

                $minnaActionUrl = route('minna.index');
                $minnaActionLabel = 'Bài học Minna';
                if (!empty($resumeMinnaLesson)) {
                    $minnaActionUrl = route('minna.show', ['number' => $resumeMinnaLesson->number]);
                    $minnaActionLabel = 'Học tiếp bài '.$resumeMinnaLesson->number;
                } elseif (!empty($firstMinnaLesson)) {
                    $minnaActionUrl = route('minna.show', ['number' => $firstMinnaLesson->number]);
                    $minnaActionLabel = 'Bắt đầu bài '.$firstMinnaLesson->number;
                }

                $focusPrimaryUrl = $todayFocus['primary_url'] ?? ($planLesson ? route('minna.show', ['number' => $planLesson['number']]) : $minnaActionUrl);
                $focusPrimaryLabel = $todayFocus['primary_label'] ?? ($planLesson ? 'Học tiếp' : 'Học ngay');
                $focusSecondaryUrl = $todayFocus['secondary_url'] ?? route('flashcard.index');
                $focusSecondaryLabel = $todayFocus['secondary_label'] ?? 'Ôn SRS';
                $focusSteps = !empty($todayFocus['steps']) ? $todayFocus['steps'] : $planTasks;

                $quickActions = [
                    ['label' => $minnaActionLabel, 'url' => $minnaActionUrl, 'tone' => 'primary'],
                    ['label' => 'Ôn Flashcard', 'url' => route('flashcard.index'), 'tone' => 'default'],
                    ['label' => 'Từ vựng', 'url' => route('vocabulary.index'), 'tone' => 'default'],
                    ['label' => 'Bảng chữ cái', 'url' => route('alphabet.index'), 'tone' => 'default'],
                    ['label' => 'Tiến độ', 'url' => route('user.progress'), 'tone' => 'default'],
                    ['label' => 'Thống kê', 'url' => route('user.statistics'), 'tone' => 'default'],
                ];
            @endphp

            <section class="mb-5 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-sm font-semibold text-red-600">Dashboard học tập</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-950 sm:text-3xl">Xin chào, {{ $user->name }}</h1>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Tập trung vào việc cần làm hôm nay, theo dõi mục tiêu tuần và xem nhanh các phần đang yếu.</p>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:flex sm:flex-wrap sm:justify-end">
                    <a href="{{ route('leaderboard.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 hover:border-slate-300 hover:bg-slate-50">Bảng xếp hạng</a>
                    <a href="{{ route('study-room.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-bold text-slate-700 hover:border-slate-300 hover:bg-slate-50">Phòng học nhóm</a>
                </div>
            </section>

            <div class="mb-5 grid gap-3">
                @if($streakAtRisk ?? false)
                    <div class="rounded-lg border border-orange-300 bg-orange-50 px-4 py-3 text-sm text-orange-900">
                        <strong>Chuỗi ngày học đang treo:</strong> ôn thẻ hoặc làm một phần Minna hôm nay để giữ chuỗi {{ $currentStreak ?? 0 }} ngày.
                    </div>
                @endif

                @if (session('status'))
                    <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">
                        {{ session('status') }}
                    </div>
                @endif

                @if(empty($onboardingSummary['completed']))
                    <div class="rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-900">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p class="font-bold">Hoàn thiện lộ trình cá nhân</p>
                                <p class="mt-1 text-blue-800">Cập nhật trình độ, mục tiêu JLPT và thời gian rảnh mỗi ngày.</p>
                            </div>
                            <a href="{{ route('onboarding.edit') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white hover:bg-blue-700">Thiết lập ngay</a>
                        </div>
                    </div>
                @endif
            </div>

            <section class="mb-5 grid gap-4 lg:grid-cols-[minmax(0,1.45fr)_minmax(20rem,0.55fr)]">
                <div class="overflow-hidden rounded-xl border border-slate-800 bg-slate-950 text-white shadow-sm">
                    <div class="grid gap-0 xl:grid-cols-[1fr_0.72fr]">
                        <div class="p-5 sm:p-6">
                            <p class="text-xs font-bold uppercase tracking-wide text-red-200">Việc chính hôm nay</p>
                            @if(!empty($todayFocus['badge']))
                                <span class="mt-3 inline-flex rounded-full bg-white/10 px-3 py-1 text-xs font-bold text-red-100">{{ $todayFocus['badge'] }}</span>
                            @endif
                            <h2 class="mt-3 text-2xl font-extrabold tracking-tight sm:text-3xl">{{ $todayFocus['title'] ?? ($planLesson ? 'Bài '.$planLesson['number'].' - '.$planLesson['title'] : 'Tiếp tục lộ trình Minna') }}</h2>
                            <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-300">{{ $todayFocus['subtitle'] ?? ('SRS đến hạn '.($planSrs['due_count'] ?? 0).' thẻ, thẻ mới '.($planSrs['new_count'] ?? 0).'.') }}</p>
                            <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                                <a href="{{ $focusPrimaryUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">{{ $focusPrimaryLabel }}</a>
                                <a href="{{ $focusSecondaryUrl }}" class="inline-flex min-h-11 items-center justify-center rounded-lg bg-white/10 px-5 py-2.5 text-sm font-bold text-white hover:bg-white/15">{{ $focusSecondaryLabel }}</a>
                            </div>
                        </div>
                        <div class="border-t border-white/10 bg-white/[0.04] p-5 sm:p-6 xl:border-l xl:border-t-0">
                            <p class="text-sm font-bold text-slate-200">Làm trong 5 phút</p>
                            <div class="mt-4 space-y-2">
                                @forelse($focusSteps as $index => $step)
                                    @php $stepText = is_array($step) ? ($step['title'] ?? 'Nhiệm vụ học tập') : $step; @endphp
                                    <div class="flex items-start gap-3 rounded-lg bg-white/10 p-3">
                                        <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-sm font-extrabold text-slate-950">{{ $index + 1 }}</span>
                                        <span class="text-sm font-semibold text-slate-100">{{ $stepText }}</span>
                                    </div>
                                @empty
                                    <p class="rounded-lg bg-white/10 p-3 text-sm text-slate-200">Chọn một bài Minna hoặc ôn vài thẻ SRS để khởi động.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <aside class="grid gap-4">
                    <div class="dashboard-card p-4 sm:p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Mục tiêu ngày</p>
                                <p class="mt-1 text-xl font-black {{ $dailyDone ? 'text-green-700' : 'text-slate-950' }}">{{ $dailyDone ? 'Đã xong' : $dailyWidth.'%' }}</p>
                            </div>
                            <span class="rounded-full {{ $dailyDone ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }} px-3 py-1 text-xs font-bold">{{ $dailyDone ? 'Hoàn thành' : 'Còn việc' }}</span>
                        </div>
                        <div class="mt-4 space-y-3">
                            <div>
                                <div class="mb-1 flex justify-between text-xs font-semibold text-slate-600">
                                    <span>Minna</span><span>{{ $minnaDone }}/{{ $minnaTarget }} bài</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-amber-500" style="width: {{ $minnaDailyWidth }}%"></div></div>
                            </div>
                            <div>
                                <div class="mb-1 flex justify-between text-xs font-semibold text-slate-600">
                                    <span>SRS</span><span>{{ $flashDone }}/{{ $flashTarget }} thẻ</span>
                                </div>
                                <div class="h-2 rounded-full bg-slate-100"><div class="h-2 rounded-full bg-emerald-500" style="width: {{ $flashDailyWidth }}%"></div></div>
                            </div>
                        </div>
                        <p class="mt-3 text-xs leading-5 {{ $dailyDone ? 'text-green-700' : 'text-slate-500' }}">
                            {{ $dailyDone ? 'Bạn đã đạt ít nhất một mục tiêu trong ngày.' : 'Hoàn thành bài Minna hoặc ôn đủ thẻ SRS là được tính ngày học.' }}
                        </p>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="dashboard-card p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">XP</p>
                            <p class="mt-1 text-2xl font-black text-slate-950">{{ $gm['xp_total'] ?? 0 }}</p>
                            <p class="mt-1 text-xs text-slate-500">Cấp {{ $gm['level'] ?? 1 }}</p>
                        </div>
                        <div class="dashboard-card p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Streak</p>
                            <p class="mt-1 text-2xl font-black text-slate-950">{{ $currentStreak ?? 0 }}</p>
                            <p class="mt-1 text-xs text-slate-500">Kỷ lục {{ $longestStreak ?? 0 }} ngày</p>
                        </div>
                        <div class="dashboard-card p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Minna</p>
                            <p class="mt-1 text-2xl font-black text-slate-950">{{ $completedMinnaLessons ?? 0 }}</p>
                            <p class="mt-1 text-xs text-slate-500">{{ $minnaProgressPercent ?? 0 }}% hoàn thành</p>
                        </div>
                        <div class="dashboard-card p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Kanji</p>
                            <p class="mt-1 text-2xl font-black text-slate-950">{{ $totalKanjis ?? 0 }}</p>
                            <p class="mt-1 text-xs text-slate-500">Tổng dữ liệu</p>
                        </div>
                    </div>
                </aside>
            </section>

            <section class="mb-5 grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(18rem,0.36fr)]">
                @if(!empty($weekly))
                    <div class="dashboard-card p-4 sm:p-5">
                        <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-red-600">Mục tiêu tuần</p>
                                <h2 class="mt-1 text-xl font-black text-slate-950">Tuần {{ $weekly['week_label'] ?? '' }} · {{ $weekly['percent'] ?? 0 }}%</h2>
                                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">{{ $weeklySummary['message'] ?? 'Theo dõi bài học, flashcard, quiz và số ngày giữ streak trong tuần.' }}</p>
                            </div>
                            <div class="w-full md:max-w-xs">
                                <div class="h-3 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-3 rounded-full bg-red-500" style="width: {{ min(100, (int) ($weekly['percent'] ?? 0)) }}%"></div>
                                </div>
                                <p class="mt-2 text-xs font-semibold text-slate-500">{{ $weeklySummary['title'] ?? 'Tổng kết tuần' }}</p>
                            </div>
                        </div>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                            @foreach($weeklyMetrics as $metric)
                                <a href="{{ $metric['url'] ?? '#' }}" class="rounded-lg border border-slate-200 bg-slate-50 p-3 hover:border-red-200 hover:bg-red-50">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-sm font-bold text-slate-900">{{ $metric['label'] }}</p>
                                        <span class="rounded-full {{ !empty($metric['done']) ? 'bg-green-100 text-green-700' : 'bg-white text-slate-600' }} px-2 py-0.5 text-xs font-bold">{{ $metric['percent'] }}%</span>
                                    </div>
                                    <p class="mt-2 text-xl font-black text-slate-950">{{ $metric['completed'] }}<span class="text-sm font-bold text-slate-500">/{{ $metric['target'] }} {{ $metric['unit'] }}</span></p>
                                    <div class="mt-2 h-2 overflow-hidden rounded-full bg-white">
                                        <div class="h-2 rounded-full {{ !empty($metric['done']) ? 'bg-green-500' : 'bg-red-500' }}" style="width: {{ min(100, (int) $metric['percent']) }}%"></div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <details class="dashboard-panel mt-4 rounded-lg border border-slate-200 bg-slate-50">
                            <summary class="flex cursor-pointer items-center justify-between gap-3 px-4 py-3 text-sm font-bold text-slate-800">
                                <span>Kế hoạch tuần sau</span>
                                <span class="text-xs text-slate-500">Mở rộng</span>
                            </summary>
                            <div class="border-t border-slate-200 p-4">
                                <p class="text-sm leading-6 text-slate-600">
                                    Tập trung tiếp vào {{ $weeklySummary['focus_label'] ?? 'Học bài' }}
                                    @if(($weeklySummary['focus_remaining'] ?? 0) > 0)
                                        còn {{ $weeklySummary['focus_remaining'] }} {{ $weeklySummary['focus_unit'] ?? '' }}.
                                    @else
                                        để giữ nhịp ổn định.
                                    @endif
                                </p>
                                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                                    @foreach(($nextWeekPlan['focus'] ?? []) as $item)
                                        <div class="rounded-lg bg-white px-3 py-2 text-sm font-semibold text-slate-700">{{ $item }}</div>
                                    @endforeach
                                </div>
                            </div>
                        </details>
                    </div>
                @endif

                <div class="dashboard-card p-4 sm:p-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Lối tắt</p>
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        @foreach($quickActions as $action)
                            <a href="{{ $action['url'] }}" class="inline-flex min-h-10 items-center justify-center rounded-lg px-3 py-2 text-center text-sm font-bold {{ $action['tone'] === 'primary' ? 'bg-red-600 text-white hover:bg-red-700' : 'border border-slate-200 bg-slate-50 text-slate-700 hover:border-red-200 hover:bg-red-50 hover:text-red-700' }}">
                                {{ $action['label'] }}
                            </a>
                        @endforeach
                    </div>
                    @if($latestMinnaAccessAt)
                        <p class="mt-3 text-xs text-slate-500">Lần học Minna gần nhất: {{ $latestMinnaAccessAt->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </section>

            @if(!empty($planTasks))
                <section class="dashboard-card mb-5 p-4 sm:p-5">
                    <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-lg font-black text-slate-950">Việc nên làm tiếp theo</h2>
                            <p class="mt-1 text-sm text-slate-500">Gợi ý theo tiến độ Minna và lịch ôn SRS.</p>
                        </div>
                        <div class="flex gap-3 text-sm font-bold">
                            <a href="{{ route('user.progress') }}" class="text-red-600 hover:text-red-700">Tiến độ</a>
                            <a href="{{ route('user.activity') }}" class="text-slate-600 hover:text-slate-900">Lịch sử</a>
                        </div>
                    </div>
                    <div class="grid gap-3 md:grid-cols-3">
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
                            <a href="{{ $taskUrl }}" class="block rounded-lg border {{ !empty($task['done']) ? 'border-green-200 bg-green-50' : 'border-slate-200 bg-slate-50 hover:border-red-200 hover:bg-red-50' }} p-4">
                                <div class="flex items-start gap-3">
                                    <span class="mt-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ !empty($task['done']) ? 'bg-green-600' : 'bg-red-600' }} text-white">
                                        @if(!empty($task['done']))
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 5l8 7-8 7V5z"></path>
                                            </svg>
                                        @endif
                                    </span>
                                    <div>
                                        <p class="font-bold text-slate-950">{{ $task['title'] ?? 'Nhiệm vụ học tập' }}</p>
                                        @if(!empty($task['subtitle']))
                                            <p class="mt-1 text-sm leading-5 text-slate-500">{{ $task['subtitle'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </section>
            @endif

            <section class="mb-5 grid gap-4 xl:grid-cols-2">
                @if(!empty($recommendedSection) || !empty($road['next_section']) || !empty($road['weak_vocab']))
                    <div class="dashboard-card p-4 sm:p-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-red-600">Lộ trình cá nhân</p>
                                @if(!empty($recommendedSection))
                                    <h2 class="mt-1 text-xl font-black text-slate-950">{{ $road['headline'] ?? ('Bài '.$recommendedSection['lesson_number'].' - '.$recommendedSection['section_title']) }}</h2>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $road['reason'] ?? '' }}</p>
                                @elseif(!empty($road['next_section']))
                                    @php $ns = $road['next_section']; @endphp
                                    <h2 class="mt-1 text-xl font-black text-slate-950">Bài {{ $ns['lesson_number'] ?? '' }} - {{ $ns['section_title'] ?? '' }}</h2>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $road['kanji_tip'] ?? 'Tiếp tục theo lộ trình được gợi ý.' }}</p>
                                @else
                                    <h2 class="mt-1 text-xl font-black text-slate-950">Ôn lại phần cần củng cố</h2>
                                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $road['kanji_tip'] ?? '' }}</p>
                                @endif
                                <div class="mt-3 flex flex-wrap gap-2 text-xs font-semibold text-slate-600">
                                    <span class="rounded-full bg-slate-100 px-3 py-1">{{ $onboardingSummary['level_label'] ?? 'Mới bắt đầu' }}</span>
                                    <span class="rounded-full bg-slate-100 px-3 py-1">{{ $onboardingSummary['jlpt_goal_label'] ?? 'JLPT N5' }}</span>
                                    <span class="rounded-full bg-slate-100 px-3 py-1">{{ $onboardingSummary['daily_study_minutes'] ?? 20 }} phút/ngày</span>
                                </div>
                            </div>
                            @if(!empty($recommendedSection))
                                <a href="{{ route('minna.section', ['number' => $recommendedSection['lesson_number'] ?? 1, 'sectionKey' => $recommendedSection['section_key'] ?? '']) }}" class="inline-flex shrink-0 items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Mở bài gợi ý</a>
                            @elseif(!empty($road['next_section']))
                                @php $ns = $road['next_section']; @endphp
                                <a href="{{ route('minna.section', ['number' => $ns['lesson_number'] ?? 1, 'sectionKey' => $ns['section_key'] ?? '']) }}" class="inline-flex shrink-0 items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Mở phần học</a>
                            @endif
                        </div>
                        @if(!empty($road['weak_vocab']))
                            <div class="mt-4 rounded-lg bg-slate-50 p-4">
                                <p class="text-sm font-bold text-slate-900">Từ vựng cần củng cố</p>
                                <div class="mt-2 flex flex-wrap gap-2">
                                    @foreach(array_slice($road['weak_vocab'], 0, 5) as $w)
                                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-700">{{ $w['front'] ?? '' }} · bài {{ $w['lesson_number'] ?? '?' }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                @if(!empty($mistakeSummary ?? []))
                    <div class="dashboard-card p-4 sm:p-5">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wide text-red-600">Lỗi sai của tôi</p>
                                <h2 class="mt-1 text-xl font-black text-slate-950">Ôn đúng phần đang yếu</h2>
                                <p class="mt-2 text-sm leading-6 text-slate-600">Từ vựng sai, ngữ pháp hay nhầm, bài điểm thấp và flashcard hay quên được gom vào một lộ trình sửa lỗi ngắn.</p>
                            </div>
                            <div class="flex shrink-0 gap-2">
                                <a href="{{ route('user.mistakes') }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Sửa lỗi</a>
                                <a href="{{ $mistakeSummary['review_url'] ?? route('flashcard.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-200 px-4 py-2 text-sm font-bold text-slate-700 hover:bg-slate-50">Ôn ngay</a>
                            </div>
                        </div>
                        <div class="mt-4 grid grid-cols-3 gap-2 sm:grid-cols-5">
                            <div class="rounded-lg bg-red-50 p-3 text-center">
                                <p class="text-lg font-black text-red-700">{{ $mistakeSummary['wrong_vocab_count'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-red-900">Từ sai</p>
                            </div>
                            <div class="rounded-lg bg-amber-50 p-3 text-center">
                                <p class="text-lg font-black text-amber-700">{{ $mistakeSummary['wrong_grammar_count'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-amber-900">Ngữ pháp</p>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-3 text-center">
                                <p class="text-lg font-black text-blue-700">{{ $mistakeSummary['wrong_quiz_count'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-blue-900">Quiz</p>
                            </div>
                            <div class="rounded-lg bg-violet-50 p-3 text-center">
                                <p class="text-lg font-black text-violet-700">{{ $mistakeSummary['low_lesson_count'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-violet-900">Bài yếu</p>
                            </div>
                            <div class="rounded-lg bg-emerald-50 p-3 text-center">
                                <p class="text-lg font-black text-emerald-700">{{ $mistakeSummary['weak_flashcard_count'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-emerald-900">Thẻ quên</p>
                            </div>
                        </div>
                    </div>
                @endif
            </section>

            @if(!empty($practicalTopicSummary ?? []))
                <section class="dashboard-card mb-5 p-4 sm:p-5">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-red-600">Chủ đề thực tế</p>
                            <h2 class="mt-1 text-xl font-black text-slate-950">Học theo tình huống</h2>
                            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">Gọi món, hỏi đường, check-in khách sạn, công việc, du học và hội thoại đời sống với quiz, flashcard, audio.</p>
                        </div>
                        <div class="grid grid-cols-3 gap-2 md:min-w-72">
                            <div class="rounded-lg bg-slate-50 p-3 text-center">
                                <p class="text-lg font-black text-slate-950">{{ $practicalTopicSummary['total_topics'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-600">Chủ đề</p>
                            </div>
                            <div class="rounded-lg bg-red-50 p-3 text-center">
                                <p class="text-lg font-black text-red-700">{{ $practicalTopicSummary['total_vocabulary'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-red-900">Từ vựng</p>
                            </div>
                            <div class="rounded-lg bg-blue-50 p-3 text-center">
                                <p class="text-lg font-black text-blue-700">{{ $practicalTopicSummary['total_dialogues'] ?? 0 }}</p>
                                <p class="mt-1 text-xs font-semibold text-blue-900">Hội thoại</p>
                            </div>
                        </div>
                    </div>
                    @if(!empty($practicalTopicSummary['recommended']))
                        <div class="mt-4 grid gap-3 md:grid-cols-3">
                            @foreach($practicalTopicSummary['recommended'] as $topic)
                                <a href="{{ $topic['url'] }}" class="rounded-lg border border-slate-200 bg-slate-50 p-4 hover:border-red-200 hover:bg-red-50">
                                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">{{ $topic['level'] }}</p>
                                    <p class="mt-1 font-black text-slate-950">{{ $topic['title'] }}</p>
                                    <p class="mt-2 text-xs leading-5 text-slate-600">{{ $topic['subtitle'] }}</p>
                                </a>
                            @endforeach
                        </div>
                    @endif
                    <div class="mt-4">
                        <a href="{{ $practicalTopicSummary['index_url'] ?? route('topics.index') }}" class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white hover:bg-red-700">Mở chủ đề thực tế</a>
                    </div>
                </section>
            @endif

            @if(!empty($reasonVocab) || !empty($weakQuizLessons) || !empty($behaviorProfile))
                <details class="dashboard-panel dashboard-card mb-5">
                    <summary class="flex cursor-pointer items-center justify-between gap-3 px-4 py-4 sm:px-5">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-red-600">Gợi ý cá nhân hóa</p>
                            <h2 class="mt-1 text-lg font-black text-slate-950">Từ vựng mục tiêu, bài quiz yếu và hồ sơ học tập</h2>
                        </div>
                        <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Mở</span>
                    </summary>
                    <div class="grid gap-4 border-t border-slate-200 p-4 sm:p-5 xl:grid-cols-3">
                        @if(!empty($reasonVocab))
                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-red-600">Theo lý do học</p>
                                <h3 class="mt-1 font-black text-slate-950">Từ vựng theo mục tiêu</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $reasonFocus['focus_text'] ?? '' }}</p>
                                <div class="mt-3 space-y-2">
                                    @foreach(array_slice($reasonVocab, 0, 4) as $word)
                                        <div class="rounded-lg bg-white p-3">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <p class="font-bold text-slate-950">{{ $word['jp'] }} <span class="text-sm font-medium text-slate-500">{{ $word['reading'] }}</span></p>
                                                    <p class="text-sm text-slate-600">{{ $word['meaning'] }}</p>
                                                </div>
                                                <span class="rounded-full bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700">{{ $word['tag'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="rounded-lg bg-slate-50 p-4">
                            <p class="text-xs font-bold uppercase tracking-wide text-blue-600">Bài yếu</p>
                            <h3 class="mt-1 font-black text-slate-950">Ôn theo lỗi quiz</h3>
                            @if(!empty($weakQuizLessons))
                                <div class="mt-3 space-y-2">
                                    @foreach($weakQuizLessons as $lesson)
                                        <a href="{{ $lesson['url'] }}" class="block rounded-lg border border-slate-200 bg-white p-3 hover:border-blue-200 hover:bg-blue-50">
                                            <p class="font-bold text-slate-950">Bài {{ $lesson['lesson_number'] }} - {{ $lesson['lesson_title'] }}</p>
                                            <p class="mt-1 text-sm text-slate-600">Quiz gần đây: {{ $lesson['percent'] }}%.</p>
                                        </a>
                                    @endforeach
                                </div>
                            @else
                                <p class="mt-3 rounded-lg bg-white p-3 text-sm text-slate-600">Chưa có bài quiz yếu. Khi có điểm thấp, hệ thống sẽ tự đưa vào lộ trình ôn.</p>
                            @endif
                        </div>

                        @if(!empty($behaviorProfile))
                            <div class="rounded-lg bg-slate-50 p-4">
                                <p class="text-xs font-bold uppercase tracking-wide text-emerald-600">Hồ sơ học tập</p>
                                <h3 class="mt-1 font-black text-slate-950">{{ $behaviorProfile['title'] ?? 'Hồ sơ học tập' }}</h3>
                                <p class="mt-2 text-sm leading-6 text-slate-600">{{ $behaviorProfile['summary'] ?? '' }}</p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @forelse(($behaviorProfile['tags'] ?? []) as $tag)
                                        <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">{{ $tag }}</span>
                                    @empty
                                        <span class="rounded-full bg-white px-3 py-1 text-xs font-semibold text-slate-600">Tiếp tục theo lộ trình hiện tại</span>
                                    @endforelse
                                </div>
                                <div class="mt-4 grid grid-cols-2 gap-3 text-center">
                                    <div class="rounded-lg bg-white p-3">
                                        <p class="text-xs text-slate-500">Bài hoàn thành</p>
                                        <p class="mt-1 text-lg font-bold text-slate-950">{{ $behaviorProfile['completed_lessons'] ?? 0 }}</p>
                                    </div>
                                    <div class="rounded-lg bg-white p-3">
                                        <p class="text-xs text-slate-500">Điểm quiz TB</p>
                                        <p class="mt-1 text-lg font-bold text-slate-950">{{ $behaviorProfile['avg_quiz_percent'] ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </details>
            @endif

            <details class="dashboard-panel dashboard-card mb-5">
                <summary class="flex cursor-pointer items-center justify-between gap-3 px-4 py-4 sm:px-5">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-red-600">Phân tích tiến độ</p>
                        <h2 class="mt-1 text-lg font-black text-slate-950">Biểu đồ gần đây và dự báo hoàn thành</h2>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-bold text-slate-600">Mở</span>
                </summary>
                <div class="grid gap-5 border-t border-slate-200 p-4 sm:p-5 xl:grid-cols-[minmax(0,1.55fr)_minmax(18rem,0.45fr)]">
                    <div>
                        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="font-black text-slate-950">Biểu đồ tiến độ</h3>
                                <p class="text-sm text-slate-500">Số bài Minna đã hoàn thành gần đây.</p>
                            </div>
                            <a href="{{ route('user.statistics') }}" class="text-sm font-bold text-red-600 hover:text-red-700">Thống kê đầy đủ</a>
                        </div>
                        <div class="grid gap-6 lg:grid-cols-2">
                            <div>
                                <div class="mb-3 flex items-center justify-between">
                                    <p class="text-sm font-semibold text-slate-700">14 ngày gần nhất</p>
                                    <p class="text-xs text-slate-500">Bài/ngày</p>
                                </div>
                                <div class="flex h-40 items-end gap-1 rounded-lg bg-slate-50 px-3 py-4">
                                    @foreach(($dayChart['labels'] ?? []) as $index => $label)
                                        @php
                                            $value = (int) ($dayValues[$index] ?? 0);
                                            $height = $value > 0 ? max(12, (int) round(($value / $maxDayValue) * 100)) : 4;
                                        @endphp
                                        <div class="flex min-w-0 flex-1 flex-col items-center justify-end gap-2">
                                            <div class="w-full max-w-6 rounded-t bg-red-500" style="height: {{ $height }}%" title="{{ $label }}: {{ $value }} bài"></div>
                                            <span class="text-[10px] text-slate-400 {{ $index % 2 === 0 ? '' : 'hidden sm:inline' }}">{{ $label }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <div class="mb-3 flex items-center justify-between">
                                    <p class="text-sm font-semibold text-slate-700">8 tuần gần nhất</p>
                                    <p class="text-xs text-slate-500">Bài/tuần</p>
                                </div>
                                <div class="space-y-3">
                                    @foreach(($weekChart['labels'] ?? []) as $index => $label)
                                        @php
                                            $value = (int) ($weekValues[$index] ?? 0);
                                            $width = $value > 0 ? max(6, (int) round(($value / $maxWeekValue) * 100)) : 0;
                                        @endphp
                                        <div class="flex items-center gap-3">
                                            <span class="w-24 shrink-0 text-xs text-slate-500">{{ $label }}</span>
                                            <div class="h-2.5 flex-1 rounded-full bg-slate-100">
                                                <div class="h-2.5 rounded-full bg-blue-600" style="width: {{ $width }}%" title="{{ $label }}: {{ $value }} bài"></div>
                                            </div>
                                            <span class="w-8 text-right text-xs font-semibold text-slate-700">{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-lg bg-emerald-50 p-4">
                        <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Dự báo hoàn thành</p>
                        <p class="mt-2 text-2xl font-black text-slate-950">
                            @if(!empty($forecast['estimated_completion_date']))
                                {{ \Carbon\Carbon::parse($forecast['estimated_completion_date'])->format('d/m/Y') }}
                            @elseif(($forecast['remaining_lessons'] ?? 0) === 0 && ($totalMinnaLessons ?? 0) > 0)
                                Hoàn thành
                            @else
                                Chưa đủ dữ liệu
                            @endif
                        </p>
                        <p class="mt-2 text-sm leading-6 text-emerald-800">{{ $forecast['message'] ?? '' }}</p>
                        <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                            <div class="rounded-lg bg-white/80 p-3">
                                <p class="text-xs text-slate-500">Còn</p>
                                <p class="mt-1 text-lg font-bold text-slate-950">{{ $forecast['remaining_lessons'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-lg bg-white/80 p-3">
                                <p class="text-xs text-slate-500">Bài/tuần</p>
                                <p class="mt-1 text-lg font-bold text-slate-950">{{ $forecast['avg_lessons_per_week'] ?? 0 }}</p>
                            </div>
                            <div class="rounded-lg bg-white/80 p-3">
                                <p class="text-xs text-slate-500">Mục tiêu</p>
                                <p class="mt-1 text-lg font-bold text-slate-950">{{ $advanced['daily_goal_lessons'] ?? 1 }}/ngày</p>
                            </div>
                        </div>
                    </div>
                </div>
            </details>

            <section class="dashboard-card p-4 sm:p-5">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Tài khoản</p>
                        <h2 class="mt-1 text-lg font-black text-slate-950">{{ $user->name }}</h2>
                        <p class="mt-1 text-sm text-slate-600">{{ $user->email }}</p>
                    </div>
                    @if(!empty($gm['badges']))
                        <div class="flex flex-wrap gap-2 sm:justify-end">
                            @foreach(array_slice($gm['badges'], 0, 4) as $b)
                                <span class="inline-flex items-center gap-1 rounded-full border border-red-100 bg-red-50 px-3 py-1 text-sm font-semibold text-red-800" title="{{ $b['slug'] ?? '' }}">
                                    <span>{{ $b['icon'] ?? '🏅' }}</span>{{ $b['name'] ?? '' }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-slate-500">Học và ôn SRS để mở huy hiệu đầu tiên.</p>
                    @endif
                </div>
            </section>
        </div>
    </main>

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
