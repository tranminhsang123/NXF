<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $courseData['title'] }} - {{ $item->title }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .japanese-text {
            font-family: 'Hiragino Sans', 'Yu Gothic', 'Meiryo', sans-serif;
            font-size: 1.2em;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <!-- Hero Section -->
    <section class="pt-24 pb-12 {{ $courseData['bgColor'] }}">
        <div class="container mx-auto max-w-7xl px-4 md:px-6">
            <div class="mb-8">
                <a href="{{ route('course.section', ['level' => $level, 'sectionType' => 'marugoto_n5']) }}" class="inline-flex items-center text-gray-700 hover:text-gray-900 transition mb-6 group font-medium">
                    <svg class="w-5 h-5 mr-1 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Quay lại Marugoto N5</span>
                </a>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-3 text-center">
                    {{ $item->title }}
                </h1>
                <p class="text-center text-gray-600 text-base md:text-lg">{{ $item->bai }}</p>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-12">
        <div class="container mx-auto max-w-5xl px-4 md:px-6 lg:px-8">
            @php $content = $item->content; @endphp
            <div class="bg-white rounded-lg shadow-md p-6 md:p-8 lg:p-10">
                
                <!-- Từ vựng Section -->
                @if(isset($content['tuVung']) && is_array($content['tuVung']))
                    <div class="mb-8">
                        <h4 class="text-xl md:text-2xl font-bold text-gray-900 mb-4 md:mb-6 border-b-2 border-red-600 pb-2">Từ vựng</h4>
                        
                        <!-- Desktop Table View -->
                        <div class="hidden md:block overflow-x-auto mb-4">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-red-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Từ vựng</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Romaji</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nghĩa</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($content['tuVung'] as $word)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="japanese-text text-lg">{{ $word['tu'] ?? '' }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                                {{ $word['romaji'] ?? '' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="text-gray-900">{{ $word['nghia'] ?? '' }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="md:hidden space-y-3">
                            @foreach($content['tuVung'] as $word)
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="japanese-text text-lg font-semibold text-gray-900 mb-1">
                                        {{ $word['tu'] ?? '' }}
                                    </div>
                                    @if(isset($word['romaji']))
                                        <div class="text-sm text-gray-600 mb-1">({{ $word['romaji'] }})</div>
                                    @endif
                                    <div class="text-gray-700">
                                        {{ $word['nghia'] ?? '' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <!-- Ngữ pháp Section -->
                @if(isset($content['nguPhap']) && is_array($content['nguPhap']))
                    <div class="mt-8">
                        <h4 class="text-xl md:text-2xl font-bold text-gray-900 mb-4 md:mb-6 border-b-2 border-red-600 pb-2">Ngữ pháp</h4>
                        
                        <div class="space-y-6">
                            @foreach($content['nguPhap'] as $grammar)
                                <div class="border-l-4 border-red-600 pl-6 py-4 bg-gray-50 rounded-r-lg">
                                    <h5 class="text-lg md:text-xl font-bold text-gray-900 mb-2">{{ $grammar['particle'] ?? '' }}</h5>
                                    
                                    @if(isset($grammar['vietnamese_meaning']))
                                        <p class="text-gray-600 mb-2 italic text-base md:text-lg">{{ $grammar['vietnamese_meaning'] }}</p>
                                    @endif
                                    
                                    @if(isset($grammar['explanation']))
                                        <p class="text-gray-700 mb-4 text-sm md:text-base leading-relaxed">{{ $grammar['explanation'] }}</p>
                                    @endif
                                    
                                    @if(isset($grammar['examples']) && is_array($grammar['examples']))
                                        <div class="space-y-3">
                                            <h6 class="font-semibold text-gray-700 mb-2 text-base md:text-lg">Ví dụ:</h6>
                                            @foreach($grammar['examples'] as $example)
                                                <div class="bg-white p-4 rounded-lg border border-gray-200">
                                                    <div class="japanese-text text-base md:text-lg mb-2">{{ $example['japanese'] ?? '' }}</div>
                                                    <div class="text-gray-700 text-sm md:text-base">{{ $example['vietnamese'] ?? '' }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>

    @include('layouts.footer')
</body>
</html>

