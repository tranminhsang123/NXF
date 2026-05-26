<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Từ vựng Bài {{ $lesson->number }} - {{ $lesson->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="container mx-auto px-4 max-w-5xl py-24">
        <nav class="text-sm text-gray-600 mb-6">
            <a href="{{ route('vocabulary.index') }}" class="text-red-600 hover:text-red-700">← Từ vựng</a>
            <span class="mx-1">/</span>
            <a href="{{ route('minna.show', $lesson->number) }}" class="text-red-600 hover:text-red-700">Bài {{ str_pad($lesson->number, 2, '0', STR_PAD_LEFT) }}</a>
        </nav>
        <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Từ vựng Bài {{ str_pad($lesson->number, 2, '0', STR_PAD_LEFT) }}</h1>
                <p class="text-gray-600 mt-1 japanese-text" style="font-family: 'Hiragino Sans','Yu Gothic',sans-serif">{{ $lesson->title }}</p>
            </div>
            <a href="{{ route('flashcard.study', $lesson->number) }}"
               class="px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg transition">
                Ôn Flashcard
            </a>
        </div>

        @if(empty($vocabGroups))
            <div class="bg-white rounded-xl border p-12 text-center text-gray-500">
                Chưa có từ vựng cho bài này. Có thể nội dung đang được cập nhật.
            </div>
        @else
            <div class="space-y-10">
                @foreach($vocabGroups as $groupLabel => $items)
                    @php
                        $hasHanTu = collect($items)->contains(fn($i) => !empty($i['han_tu']) || !empty($i['am_han']));
                        $hasGhiChu = collect($items)->contains(fn($i) => !empty($i['ghi_chu']) || !empty($i['loai_tu']));
                        $labelKey = array_key_exists('tu_vung', $items[0] ?? []) ? 'tu_vung' : 'jp';
                        $col1Label = $labelKey === 'tu_vung' ? 'Từ vựng' : 'Tiếng Nhật';
                    @endphp
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <h2 class="text-xl font-bold text-gray-900 px-6 py-4 bg-red-50 border-b">{{ $groupLabel }}</h2>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ $col1Label }}</th>
                                        @if($hasHanTu)
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Hán tự</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Âm Hán</th>
                                        @endif
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nghĩa</th>
                                        @if($hasGhiChu)
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Ghi chú</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($items as $v)
                                        @php
                                            $pos = $v['loai_tu'] ?? null;
                                            $posLabel = ['danh_tu' => 'Danh từ', 'dong_tu' => 'Động từ', 'tinh_tu' => 'Tính từ'][$pos] ?? null;
                                            $gc = trim(implode(' • ', array_filter([$v['ghi_chu'] ?? '', $posLabel])));
                                        @endphp
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-3">
                                                <span class="japanese-text text-lg" style="font-family: 'Hiragino Sans','Yu Gothic',sans-serif">{{ $v['tu_vung'] ?? $v['jp'] ?? '' }}</span>
                                                @if($posLabel && !$hasGhiChu)
                                                    <span class="ml-2 text-xs px-2 py-0.5 rounded {{ $pos === 'danh_tu' ? 'bg-blue-100 text-blue-800' : ($pos === 'dong_tu' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">{{ $posLabel }}</span>
                                                @endif
                                            </td>
                                            @if($hasHanTu)
                                                <td class="px-4 py-3 japanese-text">{{ $v['han_tu'] ?? '-' }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-600">{{ $v['am_han'] ?? '-' }}</td>
                                            @endif
                                            <td class="px-4 py-3 text-gray-900">{{ $v['nghia'] ?? '' }}</td>
                                            @if($hasGhiChu)
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $gc ?: ($v['ghi_chu'] ?? '') }}</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @include('layouts.footer')
</body>
</html>
