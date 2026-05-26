@extends('adminlayout.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Phân tích học tập</h1>
        <p class="text-gray-600 mt-2">Retention, cohort, drop-off, chất lượng nội dung và segment can thiệp.</p>
    </div>
    <form method="GET" class="flex gap-2">
        <select name="days" class="rounded-lg border border-gray-300 px-3 py-2">
            @foreach([7, 14, 30, 60, 90] as $option)
                <option value="{{ $option }}" @selected($days === $option)>{{ $option }} ngày</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-gray-800 px-4 py-2 text-white">Áp dụng</button>
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-6 gap-4 mb-6">
    @foreach(['d1' => 'D1', 'd7' => 'D7', 'd30' => 'D30'] as $key => $label)
        <div class="rounded-lg bg-white p-5 shadow-sm">
            <p class="text-sm text-gray-500">Retention {{ $label }}</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $retentionSummary[$key]['rate'] }}%</p>
            <p class="mt-1 text-xs text-gray-500">{{ $retentionSummary[$key]['returned'] }}/{{ $retentionSummary[$key]['eligible'] }} user quay lại</p>
        </div>
    @endforeach
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <p class="text-sm text-gray-500">DAU / WAU</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $retentionSummary['dau'] }}/{{ $retentionSummary['wau'] }}</p>
        <p class="mt-1 text-xs text-gray-500">Hoạt động ngày / tuần</p>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <p class="text-sm text-gray-500">Streak trung bình</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $retentionSummary['average_streak'] }}</p>
        <p class="mt-1 text-xs text-gray-500">Toàn hệ thống</p>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <p class="text-sm text-gray-500">Ôn flashcard hôm nay</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $flashcardReviewsToday }}</p>
        <p class="mt-1 text-xs text-gray-500">Lượt review</p>
    </div>
</div>

<div class="rounded-lg bg-white shadow-sm p-6 mb-6">
    <h2 class="text-lg font-bold text-gray-900 mb-4">Retention dashboard — D1 / D7 / D30</h2>
    @php $max = max(1, max($activeData ?: [0])); @endphp
    <div class="h-48 flex items-end gap-1 rounded-lg bg-gray-50 px-3 py-4">
        @foreach($labels as $i => $label)
            @php $value = (int) ($activeData[$i] ?? 0); $height = $value > 0 ? max(8, (int) round($value / $max * 100)) : 3; @endphp
            <div class="flex min-w-0 flex-1 flex-col items-center justify-end gap-2">
                <div class="w-full max-w-8 rounded-t bg-blue-600" style="height: {{ $height }}%" title="{{ $label }}: {{ $value }}"></div>
                <span class="text-[10px] text-gray-400 {{ $i % 2 ? 'hidden sm:inline' : '' }}">{{ $label }}</span>
            </div>
        @endforeach
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <div class="rounded-lg bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Phân tích cohort theo tuần đăng ký</h2>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[520px]">
                <thead>
                    <tr class="text-left text-xs uppercase text-gray-500">
                        <th class="py-2">Tuần</th>
                        <th class="py-2">User</th>
                        <th class="py-2">W1</th>
                        <th class="py-2">W2</th>
                        <th class="py-2">W4</th>
                        <th class="py-2">W8</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($cohortRows as $row)
                        <tr class="text-sm">
                            <td class="py-2 font-medium text-gray-900">{{ $row['label'] }}</td>
                            <td class="py-2 text-gray-700">{{ $row['users'] }}</td>
                            @foreach([1, 2, 4, 8] as $week)
                                <td class="py-2 text-gray-700">{{ $row['rates'][$week] === null ? '-' : $row['rates'][$week].'%' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Phễu onboarding — từ đăng ký đến bài học đầu tiên</h2>
        <div class="space-y-4">
            @foreach($onboardingFunnel as $step)
                <div>
                    <div class="mb-1 flex items-center justify-between text-sm">
                        <span class="font-semibold text-gray-900">{{ $step['label'] }}</span>
                        <span class="text-gray-500">{{ $step['count'] }} user · {{ $step['rate'] }}%</span>
                    </div>
                    <div class="h-2 rounded bg-gray-100">
                        <div class="h-2 rounded bg-emerald-600" style="width: {{ min(100, $step['rate']) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <div class="rounded-lg bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-bold text-gray-900">Danh sách người học nguy cơ bỏ học</h2>
            @adminCan('growth.edit')
                <a href="{{ route('admin.growth.create', ['segment' => 'at_risk_5_10']) }}" class="text-sm font-semibold text-blue-700 hover:underline">Tạo campaign</a>
            @endadminCan
        </div>
        <div class="space-y-3">
            @forelse($atRiskUsers as $user)
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <div>
                        <a href="{{ route('admin.users.edit', $user) }}" class="font-semibold text-blue-700 hover:underline">{{ $user->name }}</a>
                        <p class="text-xs text-gray-500">{{ $user->email }} · học lần cuối {{ optional($user->last_study_date)->format('d/m/Y') }}</p>
                    </div>
                    <span class="rounded bg-red-100 px-2 py-1 text-xs font-bold text-red-800">{{ $user->current_streak }} streak</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">Chưa có user trong ngưỡng 5-10 ngày.</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Segment user động theo hành vi học</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            @foreach($segmentDefinitions as $key => $definition)
                @php
                    $priority = $definition['priority'] ?? 'dai_han';
                    $dot = $priority === 'cao' ? 'bg-red-500' : ($priority === 'vua' ? 'bg-amber-500' : 'bg-green-600');
                @endphp
                <a href="{{ route('admin.users.index', ['learning_segment' => $key]) }}" class="rounded-lg border border-gray-200 p-3 hover:bg-gray-50">
                    <span class="flex items-start justify-between gap-2">
                        <span class="text-sm font-semibold text-gray-900">{{ $definition['label'] }}</span>
                        <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $dot }}"></span>
                    </span>
                    <span class="mt-2 block text-2xl font-bold text-gray-900">{{ $segmentCounts[$key] ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mb-6">
    <div class="rounded-lg bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Bản đồ điểm rớt trong lộ trình học</h2>
        <div class="space-y-3">
            @forelse($dropOffLessons as $row)
                <div class="border-b border-gray-100 pb-2">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-semibold text-gray-900">Bài {{ $row->lesson?->number ?? '?' }} - {{ $row->lesson?->title ?? 'Không rõ' }}</p>
                        <span class="text-sm text-gray-600">{{ $row->drop_rate }}%</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ $row->drop_count }}/{{ $row->started_count }} user bắt đầu nhưng chưa hoàn thành</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">Chưa có dữ liệu drop-off theo bài.</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Điểm rớt theo section</h2>
        <div class="space-y-3">
            @forelse($dropOffSections as $row)
                <div class="border-b border-gray-100 pb-2">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-semibold text-gray-900">Bài {{ $row->lesson?->number ?? '?' }} · {{ $row->section?->title ?? $row->section_key }}</p>
                        <span class="text-sm text-gray-600">{{ $row->drop_rate }}%</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">{{ $row->drop_count }}/{{ $row->started_count }} user rớt ở section này</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">Chưa có dữ liệu drop-off theo section.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="rounded-lg bg-white p-6 shadow-sm mb-6">
    <h2 class="text-lg font-bold text-gray-900 mb-4">Dashboard chất lượng nội dung</h2>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Báo lỗi chưa xử lý</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $contentQuality['open_report_count'] }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Bài thiếu audio</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $contentQuality['missing_audio_lessons']->count() }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Quiz sai trên 80%</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $contentQuality['high_wrong_quiz_lessons']->count() }}</p>
        </div>
        <div class="rounded-lg border border-gray-200 p-4">
            <p class="text-sm text-gray-500">Bài không có lượt học 30 ngày</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $contentQuality['idle_lessons']->count() }}</p>
        </div>
    </div>
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div>
            <h3 class="mb-3 font-semibold text-gray-900">Thiếu audio</h3>
            <div class="space-y-2">
                @forelse($contentQuality['missing_audio_lessons'] as $row)
                    <p class="text-sm text-gray-700">Bài {{ $row['lesson']->number }} - {{ $row['lesson']->title }}: <span class="font-semibold">{{ $row['missing'] }}/{{ $row['required'] }}</span></p>
                @empty
                    <p class="text-sm text-gray-500">Audio đang đủ theo checklist.</p>
                @endforelse
            </div>
        </div>
        <div>
            <h3 class="mb-3 font-semibold text-gray-900">Quiz có tỷ lệ sai cao</h3>
            <div class="space-y-2">
                @forelse($contentQuality['high_wrong_quiz_lessons'] as $row)
                    <p class="text-sm text-gray-700">Bài {{ $row->lesson?->number ?? '?' }} - {{ $row->lesson?->title ?? 'Không rõ' }}: <span class="font-semibold">{{ round(100 - (float) $row->avg_percent, 1) }}% sai</span></p>
                @empty
                    <p class="text-sm text-gray-500">Chưa có quiz sai trên 80%.</p>
                @endforelse
            </div>
        </div>
        <div>
            <h3 class="mb-3 font-semibold text-gray-900">Bài ít được học</h3>
            <div class="space-y-2">
                @forelse($contentQuality['idle_lessons'] as $lesson)
                    <p class="text-sm text-gray-700">Bài {{ $lesson->number }} - {{ $lesson->title }}</p>
                @empty
                    <p class="text-sm text-gray-500">Không có bài bị bỏ trống trong 30 ngày.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="rounded-lg bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Gợi ý nội dung cần bổ sung từ dữ liệu học</h2>
        <div class="space-y-3">
            @forelse($contentSuggestions as $item)
                <div class="border-b border-gray-100 pb-2">
                    <p class="font-semibold text-gray-900">{{ $item['title'] }} <span class="text-gray-400">-</span> {{ $item['subtitle'] }}</p>
                    <p class="mt-1 text-xs text-gray-500">{{ $item['score'] }} lượt lưu · {{ $item['reason'] }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">Chưa có dữ liệu yêu thích để gợi ý.</p>
            @endforelse
        </div>
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Bài quiz đang yếu</h2>
        <div class="space-y-3">
            @forelse($weakQuizLessons as $row)
                <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                    <p class="font-semibold text-gray-900">Bài {{ $row->lesson?->number ?? '?' }} - {{ $row->lesson?->title ?? 'Không rõ' }}</p>
                    <span class="text-sm text-gray-600">{{ round((float) $row->avg_percent, 1) }}% / {{ $row->attempts }} lượt làm</span>
                </div>
            @empty
                <p class="text-sm text-gray-500">Chưa có dữ liệu quiz.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
