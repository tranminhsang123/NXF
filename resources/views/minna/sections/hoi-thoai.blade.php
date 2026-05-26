@if(isset($content['dialogue']) && is_array($content['dialogue']))
    <div class="space-y-6">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Hội thoại</h3>
        <div class="bg-gray-50 rounded-lg p-6">
            @foreach($content['dialogue'] as $index => $line)
                @if($line['speaker'] === '—')
                    <div class="my-4 border-t border-gray-300"></div>
                @else
                    <div class="mb-4 {{ $index % 2 === 0 ? 'ml-0' : 'ml-8' }}">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <div class="bg-red-600 text-white rounded-full w-10 h-10 flex items-center justify-center font-bold text-sm mr-3">
                                    {{ $line['speaker'] }}
                                </div>
                            </div>
                            <div class="flex-1">
                                @if($line['romaji'])
                                    <div class="text-xs text-gray-500 mb-1 italic">{{ $line['romaji'] }}</div>
                                @endif
                                <div class="japanese-text text-lg mb-1">{{ $line['jp'] }}</div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
@else
    <p class="text-gray-500 italic">Nội dung đang được cập nhật...</p>
@endif

