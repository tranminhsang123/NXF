@extends('adminlayout.app')

@section('admin_title', 'Tạo chiến dịch')

@section('content')
@php
    $selectedTrigger = old('trigger_key', $prefill['trigger_key'] ?? '');
    $selectedSegment = old('segment', $prefill['segment'] ?? 'all_users');
    $selectedChannel = old('channel', $prefill['channel'] ?? \App\Models\GrowthCampaign::CHANNEL_NOTIFICATION);
    $initialTitle = old('title', $prefill['title'] ?? '');
    $initialMessage = old('message', $prefill['message'] ?? '');
    $initialVariantTitle = old('variant_b_title', '');
    $initialVariantMessage = old('variant_b_message', '');
    $abEnabled = (bool) old('ab_test_enabled');
@endphp

<div class="space-y-5 md:space-y-7">
    <div class="flex flex-col justify-between gap-3 md:flex-row md:items-end">
        <div>
            <div class="flex items-center gap-2 text-sm font-semibold text-slate-500">
                <a href="{{ route('admin.growth.index') }}" class="text-red-600 hover:text-red-700">Công cụ tăng trưởng</a>
                <span>/</span>
                <span>Tạo chiến dịch</span>
            </div>
            <h1 class="mt-2 text-2xl font-bold tracking-tight text-slate-950 md:text-3xl">Tạo chiến dịch tăng trưởng</h1>
            <p class="mt-1 max-w-3xl text-sm text-slate-500 md:text-base">
                Soạn nội dung gửi cho từng nhóm người học qua thông báo trong app, email hoặc cả hai.
            </p>
        </div>
        <a href="{{ route('admin.growth.index') }}" class="inline-flex h-10 w-fit items-center justify-center rounded-lg border border-slate-300 bg-white px-4 text-sm font-semibold text-slate-700 shadow-sm hover:bg-slate-50">
            Quay lại
        </a>
    </div>

    @if($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-800">
            {{ $errors->first() }}
        </div>
    @endif

    <form id="growth-campaign-form" method="POST" action="{{ route('admin.growth.store') }}" class="grid grid-cols-1 gap-5 xl:grid-cols-[minmax(0,1fr)_24rem]">
        @csrf

        <div class="admin-card rounded-lg">
            <div class="border-b border-slate-200 px-4 py-4 sm:px-5">
                <h2 class="text-base font-bold text-slate-950">Nội dung chiến dịch</h2>
                <p class="mt-1 text-sm text-slate-500">Các trường này quyết định nhóm nhận, kênh gửi và nội dung hiển thị.</p>
            </div>

            <div class="space-y-6 p-4 sm:p-5">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-semibold text-slate-700">Trigger hành vi</span>
                        <select id="trigger_key" name="trigger_key" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 shadow-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100">
                            <option value="">Không dùng trigger mẫu</option>
                            @foreach($triggerTemplates as $key => $template)
                                <option value="{{ $key }}" @selected($selectedTrigger === $key)>{{ $template['label'] }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-sm font-semibold text-slate-700">Nhóm người nhận</span>
                        <select id="segment" name="segment" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 shadow-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100">
                            @foreach($segments as $key => $label)
                                <option value="{{ $key }}" @selected($selectedSegment === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>

                    <label class="block">
                        <span class="mb-1.5 block text-sm font-semibold text-slate-700">Kênh gửi</span>
                        <select id="channel" name="channel" class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800 shadow-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100">
                            @foreach(\App\Models\GrowthCampaign::channelLabels() as $key => $label)
                                <option value="{{ $key }}" @selected($selectedChannel === $key)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                </div>

                <div id="segment-description" class="rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600">
                    {{ $segmentDefinitions[$selectedSegment]['description'] ?? 'Tất cả user phù hợp với segment đã chọn.' }}
                </div>

                <div class="grid grid-cols-1 gap-4 lg:grid-cols-[minmax(0,1fr)_12rem]">
                    <label class="block">
                        <span class="mb-1.5 block text-sm font-semibold text-slate-700">Tiêu đề bản A</span>
                        <input
                            id="title"
                            name="title"
                            value="{{ $initialTitle }}"
                            class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100"
                            required
                        >
                    </label>

                    <div class="rounded-lg border border-slate-200 bg-white px-3 py-2">
                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Trạng thái</p>
                        <p class="mt-1 inline-flex rounded-full bg-amber-50 px-2 py-1 text-xs font-bold text-amber-700">Bản nháp</p>
                    </div>
                </div>

                <label class="block">
                    <span class="mb-1.5 block text-sm font-semibold text-slate-700">Nội dung bản A</span>
                    <textarea
                        id="message"
                        name="message"
                        rows="7"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900 shadow-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100"
                        required
                    >{{ $initialMessage }}</textarea>
                </label>

                <div class="rounded-lg border border-slate-200">
                    <label class="flex items-start gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3">
                        <input id="ab_test_enabled" type="checkbox" name="ab_test_enabled" value="1" class="mt-1 rounded border-slate-300 text-red-600 focus:ring-red-500" @checked($abEnabled)>
                        <span>
                            <span class="block text-sm font-bold text-slate-900">Bật A/B test nội dung</span>
                            <span class="mt-0.5 block text-sm text-slate-500">Chia đều người nhận giữa bản A và bản B để đo tỷ lệ quay lại.</span>
                        </span>
                    </label>

                    <div class="grid grid-cols-1 gap-4 p-4 lg:grid-cols-2">
                        <label class="block">
                            <span class="mb-1.5 block text-sm font-semibold text-slate-700">Tiêu đề bản B</span>
                            <input
                                id="variant_b_title"
                                name="variant_b_title"
                                value="{{ $initialVariantTitle }}"
                                class="h-11 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 shadow-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100"
                            >
                        </label>

                        <label class="block">
                            <span class="mb-1.5 block text-sm font-semibold text-slate-700">Nội dung bản B</span>
                            <textarea
                                id="variant_b_message"
                                name="variant_b_message"
                                rows="4"
                                class="w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-sm text-slate-900 shadow-sm focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-100"
                            >{{ $initialVariantMessage }}</textarea>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-slate-200 px-4 py-4 sm:flex-row sm:items-center sm:justify-end sm:px-5">
                <a href="{{ route('admin.growth.index') }}" class="inline-flex h-11 items-center justify-center rounded-lg border border-slate-300 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    Hủy
                </a>
                <button class="inline-flex h-11 items-center justify-center rounded-lg bg-red-600 px-5 text-sm font-bold text-white shadow-sm hover:bg-red-700">
                    Lưu bản nháp
                </button>
            </div>
        </div>

        <aside class="space-y-4 xl:sticky xl:top-24 xl:self-start">
            <section class="admin-card rounded-lg p-4">
                <div class="mb-4 flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-base font-bold text-slate-950">Preview</h2>
                        <p class="text-sm text-slate-500">Bản xem nhanh trước khi lưu.</p>
                    </div>
                    <span id="preview-channel" class="shrink-0 rounded-full bg-slate-100 px-2 py-1 text-[11px] font-bold text-slate-600">
                        {{ \App\Models\GrowthCampaign::channelLabels()[$selectedChannel] ?? $selectedChannel }}
                    </span>
                </div>

                <div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wide text-red-600">Bản A</p>
                    <h3 id="preview-title" class="mt-2 text-lg font-bold leading-tight text-slate-950">{{ $initialTitle ?: 'Tiêu đề chiến dịch' }}</h3>
                    <p id="preview-message" class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $initialMessage ?: 'Nội dung gửi tới người học sẽ hiển thị tại đây.' }}</p>
                </div>

                <div id="preview-variant" class="{{ $abEnabled ? '' : 'hidden' }} mt-3 rounded-lg border border-slate-200 bg-slate-50 p-4">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-500">Bản B</p>
                    <h3 id="preview-variant-title" class="mt-2 text-base font-bold leading-tight text-slate-950">{{ $initialVariantTitle ?: 'Tiêu đề bản B' }}</h3>
                    <p id="preview-variant-message" class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">{{ $initialVariantMessage ?: 'Nội dung bản B sẽ hiển thị tại đây.' }}</p>
                </div>
            </section>

            <section class="admin-card rounded-lg p-4">
                <h2 class="text-base font-bold text-slate-950">Tóm tắt gửi</h2>
                <dl class="mt-4 space-y-3 text-sm">
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Segment</dt>
                        <dd id="preview-segment" class="text-right font-semibold text-slate-900">{{ $segments[$selectedSegment] ?? $selectedSegment }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">Trigger</dt>
                        <dd id="preview-trigger" class="text-right font-semibold text-slate-900">{{ $selectedTrigger ? ($triggerTemplates[$selectedTrigger]['label'] ?? $selectedTrigger) : 'Không dùng' }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-slate-500">A/B test</dt>
                        <dd id="preview-ab" class="text-right font-semibold text-slate-900">{{ $abEnabled ? 'Bật' : 'Tắt' }}</dd>
                    </div>
                </dl>
            </section>
        </aside>
    </form>
</div>

<script>
    (function () {
        const segments = @json($segments);
        const segmentDefinitions = @json($segmentDefinitions);
        const channels = @json(\App\Models\GrowthCampaign::channelLabels());
        const triggers = @json(collect($triggerTemplates)->map(fn ($template) => $template['label'])->all());

        const fields = {
            title: document.getElementById('title'),
            message: document.getElementById('message'),
            variantTitle: document.getElementById('variant_b_title'),
            variantMessage: document.getElementById('variant_b_message'),
            segment: document.getElementById('segment'),
            channel: document.getElementById('channel'),
            trigger: document.getElementById('trigger_key'),
            ab: document.getElementById('ab_test_enabled'),
        };

        const preview = {
            title: document.getElementById('preview-title'),
            message: document.getElementById('preview-message'),
            variant: document.getElementById('preview-variant'),
            variantTitle: document.getElementById('preview-variant-title'),
            variantMessage: document.getElementById('preview-variant-message'),
            segment: document.getElementById('preview-segment'),
            trigger: document.getElementById('preview-trigger'),
            channel: document.getElementById('preview-channel'),
            ab: document.getElementById('preview-ab'),
            segmentDescription: document.getElementById('segment-description'),
        };

        function text(value, fallback) {
            value = (value || '').trim();
            return value === '' ? fallback : value;
        }

        function updatePreview() {
            preview.title.textContent = text(fields.title.value, 'Tiêu đề chiến dịch');
            preview.message.textContent = text(fields.message.value, 'Nội dung gửi tới người học sẽ hiển thị tại đây.');
            preview.variantTitle.textContent = text(fields.variantTitle.value, 'Tiêu đề bản B');
            preview.variantMessage.textContent = text(fields.variantMessage.value, 'Nội dung bản B sẽ hiển thị tại đây.');

            const segment = fields.segment.value;
            const trigger = fields.trigger.value;
            const channel = fields.channel.value;
            const abEnabled = fields.ab.checked;

            preview.segment.textContent = segments[segment] || segment;
            preview.trigger.textContent = trigger ? (triggers[trigger] || trigger) : 'Không dùng';
            preview.channel.textContent = channels[channel] || channel;
            preview.ab.textContent = abEnabled ? 'Bật' : 'Tắt';
            preview.variant.classList.toggle('hidden', !abEnabled);
            preview.segmentDescription.textContent = (segmentDefinitions[segment] && segmentDefinitions[segment].description)
                ? segmentDefinitions[segment].description
                : 'Tất cả user phù hợp với segment đã chọn.';
        }

        Object.values(fields).forEach(function (field) {
            if (!field) return;
            field.addEventListener('input', updatePreview);
            field.addEventListener('change', updatePreview);
        });

        updatePreview();
    })();
</script>
@endsection
