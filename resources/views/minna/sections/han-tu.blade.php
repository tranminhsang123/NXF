@if(is_array($content))
    <div class="space-y-6">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Hán tự (Kanji)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($content as $kanji)
                @if(!empty($kanji['kanji']))
                    <div class="bg-white border-2 border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                        <div class="text-center mb-4">
                            <div class="text-6xl font-bold text-gray-900 mb-2">{{ $kanji['kanji'] }}</div>
                            <div class="text-lg font-semibold text-red-600">{{ $kanji['han_viet'] ?? '' }}</div>
                        </div>
                        
                        <div class="space-y-2 text-sm">
                            <div>
                                <span class="font-semibold text-gray-700">Nghĩa:</span>
                                <span class="text-gray-900 ml-2">{{ $kanji['nghia_vi'] ?? '' }}</span>
                            </div>
                            
                            @if(!empty($kanji['tu_vung']))
                                <div>
                                    <span class="font-semibold text-gray-700">Từ vựng:</span>
                                    <span class="japanese-text text-lg ml-2">{{ $kanji['tu_vung'] }}</span>
                                </div>
                            @endif
                            
                            @if(!empty($kanji['kunyomi']) && is_array($kanji['kunyomi']))
                                <div>
                                    <span class="font-semibold text-gray-700">Kunyomi:</span>
                                    <span class="text-gray-900 ml-2">{{ implode(', ', $kanji['kunyomi']) }}</span>
                                </div>
                            @endif
                            
                            @if(!empty($kanji['onyomi']) && is_array($kanji['onyomi']))
                                <div>
                                    <span class="font-semibold text-gray-700">Onyomi:</span>
                                    <span class="text-gray-900 ml-2">{{ implode(', ', $kanji['onyomi']) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@else
    <p class="text-gray-500 italic">Nội dung đang được cập nhật...</p>
@endif

