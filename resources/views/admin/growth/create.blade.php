@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Tạo chiến dịch tăng trưởng</h1>
    <p class="text-gray-600 mt-2">Chọn segment động, kênh gửi và bật A/B test khi cần so sánh nội dung.</p>
</div>

@if($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
        {{ $errors->first() }}
    </div>
@endif

@php
    $selectedTrigger = old('trigger_key', $prefill['trigger_key'] ?? '');
    $selectedSegment = old('segment', $prefill['segment'] ?? 'all_users');
    $selectedChannel = old('channel', $prefill['channel'] ?? \App\Models\GrowthCampaign::CHANNEL_NOTIFICATION);
@endphp

<div class="max-w-4xl rounded-lg bg-white p-6 shadow-sm">
    <form method="POST" action="{{ route('admin.growth.store') }}" class="space-y-5">
        @csrf
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Trigger hành vi</label>
            <select name="trigger_key" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                <option value="">Không dùng trigger mẫu</option>
                @foreach($triggerTemplates as $key => $template)
                    <option value="{{ $key }}" @selected($selectedTrigger === $key)>{{ $template['label'] }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Tiêu đề</label>
            <input name="title" value="{{ old('title', $prefill['title'] ?? '') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
        </div>
        <div>
            <label class="mb-2 block text-sm font-semibold text-gray-700">Nội dung bản A</label>
            <textarea name="message" rows="5" class="w-full rounded-lg border border-gray-300 px-3 py-2" required>{{ old('message', $prefill['message'] ?? '') }}</textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Nhóm người nhận</label>
                <select name="segment" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    @foreach($segments as $key => $label)
                        <option value="{{ $key }}" @selected($selectedSegment === $key)>{{ $label }}</option>
                    @endforeach
                </select>
                @if(isset($segmentDefinitions[$selectedSegment]))
                    <p class="mt-1 text-xs text-gray-500">{{ $segmentDefinitions[$selectedSegment]['description'] }}</p>
                @endif
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Kênh</label>
                <select name="channel" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    @foreach(\App\Models\GrowthCampaign::channelLabels() as $key => $label)
                        <option value="{{ $key }}" @selected($selectedChannel === $key)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 p-4">
            <label class="flex items-center gap-2 text-sm font-semibold text-gray-800">
                <input type="checkbox" name="ab_test_enabled" value="1" class="rounded border-gray-300" @checked(old('ab_test_enabled'))>
                Bật A/B test nội dung
            </label>
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Tiêu đề bản B</label>
                    <input name="variant_b_title" value="{{ old('variant_b_title') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                </div>
                <div>
                    <label class="mb-2 block text-sm font-semibold text-gray-700">Nội dung bản B</label>
                    <textarea name="variant_b_message" rows="3" class="w-full rounded-lg border border-gray-300 px-3 py-2">{{ old('variant_b_message') }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button class="rounded-lg bg-red-600 px-5 py-2 font-semibold text-white hover:bg-red-700">Lưu bản nháp</button>
            <a href="{{ route('admin.growth.index') }}" class="rounded-lg bg-gray-200 px-5 py-2 font-semibold text-gray-700 hover:bg-gray-300">Hủy</a>
        </div>
    </form>
</div>
@endsection
