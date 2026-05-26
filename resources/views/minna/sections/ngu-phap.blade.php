@if(is_array($content))
    <div class="space-y-8">
        @foreach($content as $index => $grammar)
            <div class="border-l-4 border-red-600 pl-6 py-4 bg-gray-50 rounded-r-lg">
                <div class="flex items-start justify-between gap-3 mb-4">
                    <h3 class="text-xl font-bold text-gray-900">{{ $grammar['title'] ?? '' }}</h3>
                    <button
                        type="button"
                        class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold border border-gray-300 text-gray-700 hover:border-red-400 hover:text-red-600 transition whitespace-nowrap"
                        onclick="window.toggleGrammarExplain && window.toggleGrammarExplain(this)">
                        Ẩn giải thích
                    </button>
                </div>
                
                @if(isset($grammar['pattern']))
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Cấu trúc:</h4>
                        @if(is_array($grammar['pattern']))
                            <div class="space-y-2">
                                @foreach($grammar['pattern'] as $key => $pattern)
                                    <div class="bg-white p-3 rounded">
                                        <span class="text-sm text-gray-600">{{ $key }}:</span>
                                        <span class="japanese-text text-lg ml-2">{{ $pattern }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="bg-white p-3 rounded">
                                <span class="japanese-text text-lg">{{ $grammar['pattern'] }}</span>
                            </div>
                        @endif
                    </div>
                @endif

                @if(isset($grammar['explain']))
                    <div class="mb-4 grammar-explain">
                        <h4 class="font-semibold text-gray-700 mb-2">Giải thích:</h4>
                        @if(is_array($grammar['explain']))
                            <ul class="list-disc list-inside space-y-1 text-gray-700">
                                @foreach($grammar['explain'] as $key => $explain)
                                    <li>
                                        @if(is_numeric($key))
                                            {{ $explain }}
                                        @else
                                            <strong>{{ $key }}:</strong> {{ $explain }}
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-700">{{ $grammar['explain'] }}</p>
                        @endif
                    </div>
                @endif

                @if(isset($grammar['notes']) && is_array($grammar['notes']))
                    <div class="mb-4 grammar-explain">
                        <h4 class="font-semibold text-gray-700 mb-2">Lưu ý:</h4>
                        <ul class="list-disc list-inside space-y-1 text-gray-600">
                            @foreach($grammar['notes'] as $note)
                                <li>{{ $note }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(isset($grammar['examples']) && is_array($grammar['examples']))
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Ví dụ:</h4>
                        <div class="space-y-3">
                            @foreach($grammar['examples'] as $example)
                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                    <div class="japanese-text text-lg mb-2">{{ $example['jp'] ?? '' }}</div>
                                    <div class="text-gray-700">{{ $example['nghia'] ?? '' }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if(isset($grammar['classify']))
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Phân loại:</h4>
                        @foreach($grammar['classify'] as $category => $items)
                            <div class="mb-3">
                                <h5 class="font-medium text-gray-800 mb-2">{{ ucfirst($category) }}:</h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    @foreach($items as $item)
                                        <div class="bg-white p-3 rounded border border-gray-200">
                                            <div class="japanese-text text-lg font-semibold mb-1">{{ $item['form'] ?? '' }}</div>
                                            <div class="text-sm text-gray-700 mb-1">{{ $item['meaning'] ?? '' }}</div>
                                            <div class="text-xs text-gray-500">{{ $item['usage'] ?? '' }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(isset($grammar['table']))
                    <div class="mb-4 overflow-x-auto">
                        <h4 class="font-semibold text-gray-700 mb-2">Bảng:</h4>
                        <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg">
                            <thead class="bg-red-50">
                                <tr>
                                    @foreach($grammar['table']['headers'] as $header)
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">{{ $header }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($grammar['table']['rows'] as $row)
                                    <tr>
                                        @foreach($row as $cell)
                                            <td class="px-4 py-3 text-sm text-gray-900">{{ $cell }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                @if(isset($grammar['patterns']) && is_array($grammar['patterns']))
                    <div class="mb-4">
                        <h4 class="font-semibold text-gray-700 mb-2">Các mẫu câu:</h4>
                        <div class="space-y-3">
                            @foreach($grammar['patterns'] as $pattern)
                                <div class="bg-white p-3 rounded border border-gray-200">
                                    <div class="japanese-text text-lg mb-1">{{ $pattern['form'] ?? '' }}</div>
                                    <div class="text-sm text-gray-700">{{ $pattern['meaning'] ?? '' }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>
@endif

<script>
    window.toggleGrammarExplain = function (button) {
        const container = button.closest('.border-l-4');
        if (!container) return;

        const explains = container.querySelectorAll('.grammar-explain');
        if (!explains.length) return;

        const isHidden = container.classList.toggle('grammar-explain-hidden');

        explains.forEach(el => {
            el.style.display = isHidden ? 'none' : '';
        });

        button.textContent = isHidden ? 'Hiện giải thích' : 'Ẩn giải thích';
    };
</script>

