@extends('adminlayout.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Công cụ tăng trưởng</h1>
        <p class="text-gray-600 mt-2">Segment động, trigger theo hành vi, A/B test và đo tỷ lệ quay lại sau khi gửi.</p>
    </div>
    @adminCan('growth.edit')
        <a href="{{ route('admin.growth.create') }}" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Tạo chiến dịch</a>
    @endadminCan
</div>

@if(session('success'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif

<div class="mb-6 rounded-lg bg-white p-6 shadow-sm">
    <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Campaign trigger theo hành vi</h2>
            <p class="text-sm text-gray-500">Mẫu can thiệp nhanh theo đúng nhóm người học đang cần nhắc.</p>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach($triggerTemplates as $key => $template)
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $template['label'] }}</p>
                        <p class="mt-1 text-sm text-gray-500">{{ $segments[$template['segment']] ?? $template['segment'] }} · {{ \App\Models\GrowthCampaign::channelLabels()[$template['channel']] ?? $template['channel'] }}</p>
                    </div>
                    @adminCan('growth.edit')
                        <a href="{{ route('admin.growth.create', [
                            'trigger_key' => $key,
                            'segment' => $template['segment'],
                            'channel' => $template['channel'],
                            'title' => $template['title'],
                            'message' => $template['message'],
                        ]) }}" class="shrink-0 rounded bg-gray-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-gray-700">Dùng mẫu</a>
                    @endadminCan
                </div>
                <p class="mt-3 text-sm text-gray-700">{{ $template['message'] }}</p>
            </div>
        @endforeach
    </div>
</div>

<div class="mb-6 rounded-lg bg-white p-6 shadow-sm">
    <div class="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Segment user động</h2>
            <p class="text-sm text-gray-500">Tự cập nhật theo streak, lần học cuối, onboarding và tiến độ học.</p>
        </div>
        <div class="flex flex-wrap gap-3 text-xs font-semibold">
            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-red-500"></span>Cao</span>
            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>Vừa</span>
            <span class="inline-flex items-center gap-1"><span class="h-2.5 w-2.5 rounded-full bg-green-600"></span>Dài hạn</span>
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach($segmentDefinitions as $key => $definition)
            @php
                $priority = $definition['priority'] ?? 'dai_han';
                $dot = $priority === 'cao' ? 'bg-red-500' : ($priority === 'vua' ? 'bg-amber-500' : 'bg-green-600');
            @endphp
            <div class="rounded-lg border border-gray-200 p-4">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-sm font-semibold text-gray-900">{{ $definition['label'] }}</p>
                    <span class="mt-1 h-2.5 w-2.5 shrink-0 rounded-full {{ $dot }}"></span>
                </div>
                <p class="mt-2 text-2xl font-bold text-gray-900">{{ $segmentCounts[$key] ?? 0 }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ $definition['description'] }}</p>
                @adminCan('users.view')
                    <a href="{{ route('admin.users.index', ['learning_segment' => $key]) }}" class="mt-3 inline-block text-xs font-semibold text-blue-700 hover:underline">Xem user</a>
                @endadminCan
            </div>
        @endforeach
    </div>
</div>

<div class="rounded-lg bg-white shadow-sm overflow-hidden">
    <div class="border-b border-gray-200 px-6 py-4">
        <h2 class="text-lg font-bold text-gray-900">Đo hiệu quả campaign và A/B test</h2>
        <p class="mt-1 text-sm text-gray-500">Tỷ lệ quay lại tính trong 48h sau khi gửi; A/B chia đều theo người nhận.</p>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[1120px]">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Chiến dịch</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Segment</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kênh</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hiệu quả 48h</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">A/B</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trạng thái</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($campaigns as $campaign)
                    @php
                        $metrics = $campaignMetrics[$campaign->id] ?? ['audience' => $campaign->audience_count, 'returned_48h' => 0, 'return_rate' => 0, 'variant_a' => ['sent' => 0, 'returned' => 0], 'variant_b' => ['sent' => 0, 'returned' => 0]];
                        $abEnabled = (bool) data_get($campaign->metadata, 'ab_test.enabled', false);
                    @endphp
                    <tr>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $campaign->title }}</p>
                            <p class="text-xs text-gray-500">{{ \Illuminate\Support\Str::limit($campaign->message, 120) }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $segments[$campaign->segment] ?? $campaign->segment }}</td>
                        <td class="px-6 py-4 text-sm text-gray-700">{{ $campaign->channelLabel() }}</td>
                        <td class="px-6 py-4">
                            <p class="text-sm font-semibold text-gray-900">{{ $metrics['returned_48h'] }}/{{ $metrics['audience'] }} quay lại</p>
                            <div class="mt-2 h-2 w-32 rounded bg-gray-100">
                                <div class="h-2 rounded bg-blue-600" style="width: {{ min(100, $metrics['return_rate']) }}%"></div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">{{ $metrics['return_rate'] }}%</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-700">
                            @if($abEnabled)
                                <p>A: {{ $metrics['variant_a']['returned'] }}/{{ $metrics['variant_a']['sent'] }}</p>
                                <p>B: {{ $metrics['variant_b']['returned'] }}/{{ $metrics['variant_b']['sent'] }}</p>
                            @else
                                <span class="text-gray-400">Không bật</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="rounded px-2 py-1 text-xs font-semibold {{ $campaign->status === 'sent' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">{{ $campaign->statusLabel() }}</span>
                            @if(data_get($campaign->metadata, 'trigger_key'))
                                <span class="ml-1 rounded bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700">Trigger</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($campaign->status === 'draft')
                                @adminCan('growth.send')
                                    <form method="POST" action="{{ route('admin.growth.send', $campaign) }}" onsubmit="return confirm('Gửi chiến dịch này ngay?')">
                                        @csrf
                                        <button class="rounded bg-blue-600 px-3 py-1 text-xs font-semibold text-white hover:bg-blue-700">Gửi</button>
                                    </form>
                                @endadminCan
                            @else
                                <span class="text-xs text-gray-500">{{ $campaign->sent_at?->format('d/m/Y H:i') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-500">Chưa có chiến dịch.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($campaigns->hasPages())
        <div class="border-t border-gray-200 px-6 py-4">{{ $campaigns->links() }}</div>
    @endif
</div>
@endsection
