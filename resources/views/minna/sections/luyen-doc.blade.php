@if(isset($content['sentences']) && is_array($content['sentences']))
    <div class="space-y-6">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Câu luyện đọc</h3>
        @foreach($content['sentences'] as $index => $sentence)
            <div class="bg-gray-50 p-6 rounded-lg border-l-4 border-blue-500">
                <div class="flex items-start">
                    <span class="bg-blue-500 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold mr-4 flex-shrink-0">
                        {{ $index + 1 }}
                    </span>
                    <div class="flex-1">
                        <div class="japanese-text text-xl mb-3 leading-relaxed">{{ $sentence }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p class="text-gray-500 italic">Nội dung đang được cập nhật...</p>
@endif

