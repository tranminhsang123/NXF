<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tìm kiếm - Học tiếng Nhật</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50 font-[Inter,sans-serif]">
@include('layouts.header')

@php
    $tabs = [
        'all' => 'Tất cả',
        'vocabulary' => 'Từ vựng',
        'kanji' => 'Kanji',
        'lessons' => 'Bài Minna',
        'sentence_patterns' => 'Mẫu câu',
        'grammar' => 'Ngữ pháp',
        'favorites' => 'Yêu thích',
        'related' => 'Gợi ý',
    ];
    $activeType = $type ?? 'all';
@endphp

<main class="container mx-auto max-w-4xl px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-2">Tìm kiếm toàn hệ thống</h1>
    <p class="text-gray-600 mb-6">Tìm nhanh từ vựng, Kanji, bài Minna, mẫu câu, ngữ pháp, từ yêu thích và gợi ý liên quan.</p>

    <div class="mb-6">
        @include('components.global-search-bar', ['searchQuery' => $query, 'searchType' => $activeType])
    </div>

    @if($query !== '' && mb_strlen($query) < 2)
        <p class="rounded-lg bg-amber-50 px-4 py-3 text-amber-800">Nhập ít nhất 2 ký tự để tìm.</p>
    @elseif($results)
        @php $counts = $results['counts'] ?? []; $total = array_sum($counts); @endphp
        <p class="mb-4 text-sm text-gray-600">
            {{ $total > 0 ? "Tìm thấy {$total} kết quả cho “{$query}”" : "Không có kết quả cho “{$query}”" }}
        </p>

        <div class="mb-6 flex flex-wrap gap-2">
            @foreach($tabs as $key => $label)
                @php $count = $key === 'all' ? $total : ($counts[$key] ?? 0); @endphp
                <a href="{{ route('search.index', ['q' => $query, 'type' => $key]) }}"
                   class="rounded-full px-3 py-1.5 text-sm font-medium {{ $activeType === $key ? 'bg-red-600 text-white' : 'bg-white border border-gray-200 text-gray-700 hover:border-red-300' }}">
                    {{ $label }}@if($count > 0) ({{ $count }})@endif
                </a>
            @endforeach
        </div>

        @if(!auth()->check() && in_array($activeType, ['favorites', 'all'], true))
            <p class="mb-4 rounded-lg border border-dashed border-gray-300 bg-white px-4 py-3 text-sm text-gray-600">
                <a href="{{ route('login') }}" class="font-semibold text-red-600 hover:underline">Đăng nhập</a> để tìm trong từ yêu thích.
            </p>
        @endif

        @if(in_array($activeType, ['all', 'vocabulary'], true) && !empty($results['vocabulary']))
            <section class="mb-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-gray-900">Từ vựng</h2>
                <ul class="space-y-3">
                    @foreach($results['vocabulary'] as $row)
                        <li class="flex flex-wrap items-baseline justify-between gap-2 border-b border-gray-100 pb-3 last:border-0">
                            <div>
                                <span class="japanese-text text-lg font-semibold text-gray-900">{{ $row['term'] }}</span>
                                @if(!empty($row['reading']))
                                    <span class="ml-2 text-sm text-gray-500">{{ $row['reading'] }}</span>
                                @endif
                                <p class="text-gray-700">{{ $row['meaning'] }}</p>
                            </div>
                            @if(!empty($row['lesson_number']))
                                <a href="{{ route('minna.show', $row['lesson_number']) }}" class="text-sm font-medium text-red-600 hover:underline">
                                    Bài {{ $row['lesson_number'] }}
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if(in_array($activeType, ['all', 'kanji'], true) && !empty($results['kanji']))
            <section class="mb-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-gray-900">Kanji</h2>
                <ul class="space-y-3">
                    @foreach($results['kanji'] as $row)
                        <li class="flex items-center justify-between border-b border-gray-100 pb-3 last:border-0">
                            <div>
                                <span class="japanese-text text-2xl font-bold">{{ $row['character'] }}</span>
                                <span class="ml-3 text-gray-700">{{ $row['meaning'] }}</span>
                                <span class="ml-2 text-xs text-gray-500">{{ $row['level'] }}</span>
                            </div>
                            <a href="{{ $row['url'] ?? route('kanji.index') }}" class="text-sm text-red-600 hover:underline">Xem</a>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if(in_array($activeType, ['all', 'lessons'], true) && !empty($results['lessons']))
            <section class="mb-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-gray-900">Bài Minna</h2>
                <ul class="space-y-3">
                    @foreach($results['lessons'] as $lesson)
                        <li class="border-b border-gray-100 pb-3 last:border-0">
                            <a href="{{ $lesson['url'] ?? route('minna.show', $lesson['number']) }}" class="font-semibold text-red-600 hover:underline">
                                Bài {{ str_pad($lesson['number'], 2, '0', STR_PAD_LEFT) }}: {{ $lesson['title'] }}
                            </a>
                            @if(!empty($lesson['description']))
                                <p class="mt-1 text-sm text-gray-600">{{ Str::limit($lesson['description'], 120) }}</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if(in_array($activeType, ['all', 'sentence_patterns'], true) && !empty($results['sentence_patterns']))
            <section class="mb-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-gray-900">Mẫu câu</h2>
                <ul class="space-y-3">
                    @foreach($results['sentence_patterns'] as $row)
                        <li class="border-b border-gray-100 pb-3 last:border-0">
                            <p class="japanese-text text-lg text-gray-900">{{ $row['pattern'] }}</p>
                            <p class="text-gray-700">{{ $row['meaning'] }}</p>
                            @if(!empty($row['lesson_number']))
                                <a href="{{ route('minna.show', $row['lesson_number']) }}" class="mt-1 inline-block text-sm text-red-600 hover:underline">Bài {{ $row['lesson_number'] }}</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if(in_array($activeType, ['all', 'grammar'], true) && !empty($results['grammar']))
            <section class="mb-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-gray-900">Ngữ pháp</h2>
                <ul class="space-y-4">
                    @foreach($results['grammar'] as $row)
                        <li class="rounded-lg bg-gray-50 p-4">
                            <p class="font-semibold text-gray-900">{{ $row['title'] }}</p>
                            @if(!empty($row['pattern']))
                                <p class="japanese-text mt-1 text-gray-800">{{ $row['pattern'] }}</p>
                            @endif
                            @if(!empty($row['explain']))
                                <p class="mt-2 text-sm text-gray-600">{{ Str::limit($row['explain'], 160) }}</p>
                            @endif
                            @if(!empty($row['lesson_number']))
                                <a href="{{ route('minna.section', ['number' => $row['lesson_number'], 'sectionKey' => 'ngu-phap']) }}" class="mt-2 inline-block text-sm text-red-600 hover:underline">
                                    Bài {{ $row['lesson_number'] }} · Ngữ pháp
                                </a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if(auth()->check() && in_array($activeType, ['all', 'favorites'], true) && !empty($results['favorites']))
            <section class="mb-8 rounded-2xl border border-emerald-200 bg-emerald-50/50 p-5 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-gray-900">Từ yêu thích</h2>
                <ul class="space-y-3">
                    @foreach($results['favorites'] as $row)
                        <li class="border-b border-emerald-100 pb-3 last:border-0">
                            <span class="japanese-text font-semibold">{{ $row['front'] }}</span>
                            <span class="text-gray-700"> — {{ $row['back'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if(in_array($activeType, ['all', 'related'], true) && !empty($results['related']))
            <section class="mb-8 rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
                <h2 class="mb-4 text-lg font-bold text-gray-900">Gợi ý liên quan</h2>
                <ul class="space-y-3">
                    @foreach($results['related'] as $row)
                        <li class="flex flex-wrap items-start justify-between gap-2 border-b border-gray-100 pb-3 last:border-0">
                            <div>
                                <span class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $row['reason'] ?? '' }}</span>
                                @if(($row['type'] ?? '') === 'kanji')
                                    <p class="japanese-text text-xl font-bold">{{ $row['item']['character'] ?? '' }} <span class="text-base font-normal text-gray-700">{{ $row['item']['meaning'] ?? '' }}</span></p>
                                @elseif(($row['type'] ?? '') === 'sentence_pattern')
                                    <p class="japanese-text">{{ $row['item']['pattern'] ?? '' }}</p>
                                    <p class="text-gray-700">{{ $row['item']['meaning'] ?? '' }}</p>
                                @else
                                    <p class="japanese-text font-semibold">{{ $row['item']['term'] ?? '' }}</p>
                                    <p class="text-gray-700">{{ $row['item']['meaning'] ?? '' }}</p>
                                @endif
                            </div>
                            @if(!empty($row['url']))
                                <a href="{{ $row['url'] }}" class="text-sm text-red-600 hover:underline">Mở</a>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </section>
        @endif

        @if($total === 0)
            <div class="rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center text-gray-500">
                Thử từ khóa khác hoặc tìm theo tiếng Nhật / tiếng Việt.
            </div>
        @endif
    @elseif($query === '')
        <p class="mb-4 text-sm font-medium text-gray-700">Bạn có thể tìm theo:</p>
        @include('components.global-search-categories')
        <p class="mt-6 mb-3 text-sm font-medium text-gray-700">Gợi ý từ khóa:</p>
        <div class="flex flex-wrap gap-2">
            @foreach(['こんにちは', 'です', '食べる', '学校', 'Bài 1'] as $hint)
                <a href="{{ route('search.index', ['q' => $hint]) }}"
                   class="rounded-full border border-gray-200 bg-white px-4 py-1.5 text-sm text-gray-700 hover:border-red-300 hover:text-red-600">
                    {{ $hint }}
                </a>
            @endforeach
        </div>
    @endif
</main>

@include('layouts.footer')
</body>
</html>
