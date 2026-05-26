<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $courseData['title'] }} - {{ $title }}</title>
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
                <a href="{{ route('course.section', ['level' => $level, 'sectionType' => 'speed_master_n5']) }}" class="inline-flex items-center text-gray-700 hover:text-gray-900 transition mb-6 group font-medium">
                    <svg class="w-5 h-5 mr-1 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Quay lại Speed Master N5</span>
                </a>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-gray-900 mb-3 text-center">
                    {{ $bai }}: {{ $title }}
                </h1>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="py-12">
        <div class="container mx-auto max-w-5xl px-4 md:px-6 lg:px-8">
            <!-- Tabs for different sections -->
            <div class="mb-6 border-b border-gray-200 overflow-x-auto">
                <nav class="-mb-px flex space-x-4 md:space-x-8 min-w-max">
                    <button onclick="showSection('tuVung')" class="section-tab active border-b-2 border-red-600 py-3 md:py-4 px-2 md:px-1 text-sm font-medium text-red-600 whitespace-nowrap">
                        Từ vựng
                    </button>
                    <button onclick="showSection('nguPhap')" class="section-tab border-b-2 border-transparent py-3 md:py-4 px-2 md:px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        Ngữ pháp
                    </button>
                    <button onclick="showSection('docHieu')" class="section-tab border-b-2 border-transparent py-3 md:py-4 px-2 md:px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        Đọc hiểu
                    </button>
                    <button onclick="showSection('ngheHieu')" class="section-tab border-b-2 border-transparent py-3 md:py-4 px-2 md:px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                        Nghe hiểu
                    </button>
                </nav>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 md:p-8 lg:p-10">
                <!-- Từ vựng Section -->
                <div id="tuVung" class="section-content active">
                    @php
                        $tuVungData = $groupedData->get('tuVung', collect())->first();
                    @endphp
                    
                    @if(!$tuVungData)
                        <div class="bg-gray-50 rounded-lg p-8 text-center">
                            <p class="text-gray-500">Chưa có dữ liệu từ vựng cho bài này</p>
                        </div>
                    @else
                        @php 
                            $content = $tuVungData->content;
                            $words = is_array($content) ? $content : [];
                        @endphp
                        @if(!empty($words))
                            <div class="mt-4">
                                <!-- Desktop Table View -->
                                <div class="hidden md:block overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-red-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Từ vựng</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider">Nghĩa</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($words as $word)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span class="japanese-text text-lg">{{ $word['tu'] ?? '' }}</span>
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
                                    @foreach($words as $word)
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <div class="japanese-text text-lg font-semibold text-gray-900 mb-1">
                                                {{ $word['tu'] ?? '' }}
                                            </div>
                                            <div class="text-gray-700">
                                                {{ $word['nghia'] ?? '' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Ngữ pháp Section -->
                <div id="nguPhap" class="section-content hidden">
                    @php
                        $nguPhapData = $groupedData->get('nguPhap', collect())->first();
                    @endphp
                    
                    @if(!$nguPhapData)
                        <div class="bg-gray-50 rounded-lg p-8 text-center">
                            <p class="text-gray-500">Chưa có dữ liệu ngữ pháp cho bài này</p>
                        </div>
                    @else
                        @php 
                            $content = $nguPhapData->content;
                            $grammarPoints = is_array($content) ? $content : [];
                        @endphp
                        @if(!empty($grammarPoints))
                            <div class="space-y-6">
                                @foreach($grammarPoints as $grammar)
                                    <div class="border-l-4 border-red-600 pl-6 py-4 bg-gray-50 rounded-r-lg">
                                        <h4 class="text-xl font-bold text-gray-900 mb-2">{{ $grammar['particle'] ?? '' }}</h4>
                                        
                                        @if(isset($grammar['explanation']))
                                            <p class="text-gray-700 mb-4">{{ $grammar['explanation'] }}</p>
                                        @endif
                                        
                                        @if(isset($grammar['examples']) && is_array($grammar['examples']))
                                            <div class="space-y-3">
                                                <h5 class="font-semibold text-gray-700 mb-2">Ví dụ:</h5>
                                                @foreach($grammar['examples'] as $example)
                                                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                                                        <div class="japanese-text text-lg mb-2">{{ $example['japanese'] ?? '' }}</div>
                                                        <div class="text-gray-700">{{ $example['vietnamese'] ?? '' }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Đọc hiểu Section -->
                <div id="docHieu" class="section-content hidden">
                    @php
                        $docHieuData = $groupedData->get('docHieu', collect());
                    @endphp
                    
                    @if($docHieuData->isEmpty())
                        <div class="bg-gray-50 rounded-lg p-8 text-center">
                            <p class="text-gray-500">Chưa có dữ liệu đọc hiểu cho bài này</p>
                        </div>
                    @else
                        <div class="space-y-8">
                            @foreach($docHieuData as $item)
                                @php $content = $item->content; @endphp
                                <div>
                                    @if(isset($content['passage']))
                                        <div class="mb-6 p-4 md:p-6 bg-blue-50 rounded-lg border border-blue-200">
                                            <h4 class="font-semibold text-gray-900 mb-3">Đoạn văn:</h4>
                                            <p class="japanese-text text-base md:text-lg text-gray-800 leading-relaxed whitespace-pre-wrap break-words">{{ $content['passage'] }}</p>
                                        </div>
                                    @endif
                                    
                                    @if(isset($content['question']))
                                        <div class="mb-4">
                                            <h4 class="font-semibold text-gray-900 mb-3">Câu hỏi:</h4>
                                            <p class="text-lg text-gray-800">{{ $content['question'] }}</p>
                                        </div>
                                    @endif
                                    
                                    @if(isset($content['options']) && is_array($content['options']))
                                        <div class="space-y-3 mb-4">
                                            @foreach($content['options'] as $index => $option)
                                                <div class="p-4 rounded-lg border-2 transition {{ isset($content['correct_answer']) && $content['correct_answer'] == $index ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white hover:border-gray-300' }}">
                                                    <div class="flex items-start gap-3">
                                                        <span class="font-bold text-gray-700 mt-1">{{ $index + 1 }}.</span>
                                                        <div class="flex-1">
                                                            <div class="japanese-text text-lg mb-1">{{ $option['text'] ?? '' }}</div>
                                                            @if(isset($option['romaji']))
                                                                <div class="text-sm text-gray-600">({{ $option['romaji'] }})</div>
                                                            @endif
                                                            @if(isset($option['meaning']))
                                                                <div class="text-sm text-gray-700">{{ $option['meaning'] }}</div>
                                                            @endif
                                                        </div>
                                                        @if(isset($content['correct_answer']) && $content['correct_answer'] == $index)
                                                            <span class="text-green-600 font-bold">✓ Đúng</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    @if(isset($content['explanation']))
                                        <div class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                                            <h5 class="font-semibold text-gray-900 mb-2">Giải thích:</h5>
                                            <p class="text-gray-700">{{ $content['explanation'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Nghe hiểu Section -->
                <div id="ngheHieu" class="section-content hidden">
                    @php
                        $ngheHieuData = $groupedData->get('ngheHieu', collect());
                    @endphp
                    
                    @if($ngheHieuData->isEmpty())
                        <div class="bg-gray-50 rounded-lg p-8 text-center">
                            <p class="text-gray-500">Chưa có dữ liệu nghe hiểu cho bài này</p>
                        </div>
                    @else
                        <div class="space-y-8">
                            @foreach($ngheHieuData as $item)
                                <div>
                                    <p class="text-gray-600">Phần nghe hiểu - cần audio để học</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    @include('layouts.footer')

    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.section-content').forEach(section => {
                section.classList.add('hidden');
                section.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.section-tab').forEach(tab => {
                tab.classList.remove('active', 'border-red-600', 'text-red-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Show selected section
            document.getElementById(sectionId).classList.remove('hidden');
            document.getElementById(sectionId).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active', 'border-red-600', 'text-red-600');
            event.target.classList.remove('border-transparent', 'text-gray-500');
        }
    </script>
</body>
</html>

