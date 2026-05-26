@if(isset($content['vocab']) && is_array($content['vocab']))
    <div class="mb-8">
        <div class="flex items-center justify-between gap-3 mb-4">
            <h3 class="text-2xl font-bold text-gray-900">Từ vựng</h3>
            <button
                type="button"
                class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold border border-gray-300 text-gray-700 hover:border-red-400 hover:text-red-600 transition"
                onclick="window.toggleMeaningVisibility && window.toggleMeaningVisibility(this)">
                Ẩn nghĩa tiếng Việt
            </button>
        </div>
        
        <!-- Desktop Table View -->
        <div class="hidden md:block overflow-x-auto -mx-4 px-4" style="scrollbar-width: thin; scrollbar-color: #dc2626 #f1f1f1;">
            <style>
                .vocab-table-scroll::-webkit-scrollbar {
                    height: 8px;
                }
                .vocab-table-scroll::-webkit-scrollbar-track {
                    background: #f1f1f1;
                    border-radius: 10px;
                }
                .vocab-table-scroll::-webkit-scrollbar-thumb {
                    background: #dc2626;
                    border-radius: 10px;
                }
                .vocab-table-scroll::-webkit-scrollbar-thumb:hover {
                    background: #b91c1c;
                }
                .hide-meaning .meaning-text {
                    filter: blur(0.3rem);
                    transition: filter 0.15s ease-in-out;
                }
            </style>
            <div class="vocab-table-scroll">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-red-50">
                        <tr>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Từ vựng</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Hán tự</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Âm Hán</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nghĩa</th>
                            <th class="px-4 md:px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider whitespace-nowrap">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($content['vocab'] as $vocab)
                            @php
                                $pos = $vocab['loai_tu'] ?? null; // danh_tu, dong_tu, tinh_tu...
                                $posLabelMap = [
                                    'danh_tu' => 'Danh từ',
                                    'dong_tu' => 'Động từ',
                                    'tinh_tu' => 'Tính từ',
                                ];
                                $posColorMap = [
                                    'danh_tu' => 'bg-blue-100 text-blue-800',
                                    'dong_tu' => 'bg-green-100 text-green-800',
                                    'tinh_tu' => 'bg-purple-100 text-purple-800',
                                ];
                                $posLabel = $posLabelMap[$pos] ?? null;
                                $posClass = $posColorMap[$pos] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="japanese-text text-base md:text-lg">{{ $vocab['tu_vung'] ?? '' }}</span>
                                        @if(!empty($vocab['tu_vung']))
                                            <button type="button" class="js-pronounce inline-flex items-center justify-center rounded-full border border-gray-300 px-2 py-0.5 text-[10px] font-bold text-gray-600 hover:border-red-400 hover:text-red-600" data-pronounce-text="{{ $vocab['tu_vung'] }}">Nghe</button>
                                            <button type="button"
                                                    class="js-favorite inline-flex items-center justify-center rounded-full border border-emerald-300 px-2 py-0.5 text-[10px] font-bold text-emerald-700 hover:bg-emerald-50"
                                                    data-favorite-front="{{ $vocab['tu_vung'] }}"
                                                    data-favorite-back="{{ $vocab['nghia'] ?? '' }}">
                                                Lưu
                                            </button>
                                        @endif
                                        @if($posLabel)
                                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $posClass }}">
                                                {{ $posLabel }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap">
                                    <span class="japanese-text">{{ $vocab['han_tu'] ?? '-' }}</span>
                                </td>
                                <td class="px-4 md:px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $vocab['am_han'] ?? '-' }}
                                </td>
                                <td class="px-4 md:px-6 py-4 text-sm text-gray-900 meaning-text">
                                    {{ $vocab['nghia'] ?? '' }}
                                </td>
                                <td class="px-4 md:px-6 py-4 text-sm text-gray-500">
                                    {{ $vocab['ghi_chu'] ?? '' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Card View -->
        <div class="md:hidden space-y-4">
            @foreach($content['vocab'] as $vocab)
                @php
                    $pos = $vocab['loai_tu'] ?? null;
                    $posLabelMap = [
                        'danh_tu' => 'Danh từ',
                        'dong_tu' => 'Động từ',
                        'tinh_tu' => 'Tính từ',
                    ];
                    $posColorMap = [
                        'danh_tu' => 'bg-blue-100 text-blue-800',
                        'dong_tu' => 'bg-green-100 text-green-800',
                        'tinh_tu' => 'bg-purple-100 text-purple-800',
                    ];
                    $posLabel = $posLabelMap[$pos] ?? null;
                    $posClass = $posColorMap[$pos] ?? 'bg-gray-100 text-gray-700';
                @endphp
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-start justify-between gap-3 mb-2">
                        <div class="flex-1">
                            <div class="japanese-text text-lg font-semibold text-gray-900 mb-1">
                                {{ $vocab['tu_vung'] ?? '' }}
                            </div>
                            @if(!empty($vocab['tu_vung']))
                                <button type="button" class="js-pronounce mb-2 inline-flex items-center justify-center rounded-full border border-gray-300 px-2 py-1 text-xs font-bold text-gray-600" data-pronounce-text="{{ $vocab['tu_vung'] }}">Nghe</button>
                                <button type="button"
                                        class="js-favorite mb-2 ml-1 inline-flex items-center justify-center rounded-full border border-emerald-300 px-2 py-1 text-xs font-bold text-emerald-700"
                                        data-favorite-front="{{ $vocab['tu_vung'] }}"
                                        data-favorite-back="{{ $vocab['nghia'] ?? '' }}">
                                    Lưu
                                </button>
                            @endif
                            @if(!empty($vocab['han_tu']) && $vocab['han_tu'] !== '-')
                                <div class="japanese-text text-base text-gray-700 mb-1">
                                    {{ $vocab['han_tu'] }}
                                </div>
                            @endif
                        </div>
                        @if($posLabel)
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $posClass }}">
                                {{ $posLabel }}
                            </span>
                        @endif
                    </div>
                    <div class="space-y-1 text-sm">
                        @if(!empty($vocab['am_han']) && $vocab['am_han'] !== '-')
                            <div class="text-gray-600">
                                <span class="font-medium">Âm Hán:</span> {{ $vocab['am_han'] }}
                            </div>
                        @endif
                        <div class="text-gray-900 font-medium meaning-text">
                            <span class="font-semibold">Nghĩa:</span> {{ $vocab['nghia'] ?? '' }}
                        </div>
                        @if(!empty($vocab['ghi_chu']))
                            <div class="text-gray-600">
                                <span class="font-medium">Ghi chú:</span> {{ $vocab['ghi_chu'] }}
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

<script>
    window.toggleMeaningVisibility = function (button) {
        const root = document.documentElement;
        const isHidden = root.classList.toggle('hide-meaning');

        if (button) {
            button.textContent = isHidden ? 'Hiện nghĩa tiếng Việt' : 'Ẩn nghĩa tiếng Việt';
        }
    };
</script>

@if(isset($content['mau_cau']) && is_array($content['mau_cau']))
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Mẫu câu</h3>
        <div class="space-y-4">
            @foreach($content['mau_cau'] as $mau)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="japanese-text text-lg mb-2">{{ $mau['jp'] ?? '' }}</div>
                    @if(!empty($mau['jp']) && !empty($mau['nghia']))
                        <div class="mb-2 flex gap-2">
                            <button type="button" class="js-pronounce rounded-full border border-gray-300 px-2 py-1 text-xs font-bold text-gray-600" data-pronounce-text="{{ $mau['jp'] }}">Nghe</button>
                            <button type="button" class="js-favorite rounded-full border border-emerald-300 px-2 py-1 text-xs font-bold text-emerald-700" data-favorite-front="{{ $mau['jp'] }}" data-favorite-back="{{ $mau['nghia'] }}">Lưu</button>
                        </div>
                    @endif
                    <div class="text-gray-700">{{ $mau['nghia'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if(isset($content['countries']) && is_array($content['countries']))
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Tên nước</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($content['countries'] as $country)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="japanese-text text-lg mb-1">{{ $country['tu_vung'] ?? '' }}</div>
                    <div class="text-gray-700">{{ $country['nghia'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if(isset($content['proper_nouns']) && is_array($content['proper_nouns']))
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Danh từ riêng</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($content['proper_nouns'] as $noun)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="japanese-text text-lg mb-1">{{ $noun['tu_vung'] ?? '' }}</div>
                    <div class="text-gray-700">{{ $noun['nghia'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if(isset($content['cau']) && is_array($content['cau']))
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Câu</h3>
        <div class="space-y-4">
            @foreach($content['cau'] as $cau)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="japanese-text text-lg mb-2">{{ $cau['jp'] ?? '' }}</div>
                    @if(!empty($cau['jp']) && !empty($cau['nghia']))
                        <div class="mb-2 flex gap-2">
                            <button type="button" class="js-pronounce rounded-full border border-gray-300 px-2 py-1 text-xs font-bold text-gray-600" data-pronounce-text="{{ $cau['jp'] }}">Nghe</button>
                            <button type="button" class="js-favorite rounded-full border border-emerald-300 px-2 py-1 text-xs font-bold text-emerald-700" data-favorite-front="{{ $cau['jp'] }}" data-favorite-back="{{ $cau['nghia'] }}">Lưu</button>
                        </div>
                    @endif
                    <div class="text-gray-700">{{ $cau['nghia'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if(isset($content['places']) && is_array($content['places']))
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Địa danh</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($content['places'] as $place)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="japanese-text text-lg mb-1">{{ $place['tu_vung'] ?? '' }}</div>
                    <div class="text-gray-700">{{ $place['nghia'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif

@if(isset($content['rail']) && is_array($content['rail']))
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Từ vựng về tàu</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($content['rail'] as $rail)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="japanese-text text-lg mb-1">{{ $rail['tu_vung'] ?? '' }}</div>
                    <div class="text-gray-700">{{ $rail['nghia'] ?? '' }}</div>
                </div>
            @endforeach
        </div>
    </div>
@endif

