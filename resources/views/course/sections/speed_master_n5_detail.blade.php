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
            line-height: 1.75;
            overflow-wrap: break-word;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    @include('layouts.header')

    <main>
        <section class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-5xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
                <a href="{{ route('course.section', ['level' => $level, 'sectionType' => 'speed_master_n5']) }}" class="inline-flex items-center text-sm font-bold text-slate-600 hover:text-slate-950">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Speed Master N5
                </a>
                <p class="mt-5 text-sm font-bold text-red-600">{{ $bai }}</p>
                <h1 class="mt-1 break-words text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">{{ $title }}</h1>
            </div>
        </section>

        <section class="py-6 sm:py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                <div class="mb-4 overflow-x-auto rounded-xl border border-slate-200 bg-white p-2 shadow-sm">
                    <nav class="flex min-w-max gap-2">
                    <button onclick="showSection('tuVung', this)" class="section-tab active rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm">
                        Từ vựng
                    </button>
                    <button onclick="showSection('nguPhap', this)" class="section-tab rounded-lg px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 hover:text-slate-950">
                        Ngữ pháp
                    </button>
                    <button onclick="showSection('docHieu', this)" class="section-tab rounded-lg px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 hover:text-slate-950">
                        Đọc hiểu
                    </button>
                    <button onclick="showSection('ngheHieu', this)" class="section-tab rounded-lg px-4 py-2.5 text-sm font-bold text-slate-600 hover:bg-slate-100 hover:text-slate-950">
                        Nghe hiểu
                    </button>
                </nav>
            </div>

                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 lg:p-8">
                <!-- Từ vựng Section -->
                <div id="tuVung" class="section-content active">
                    @php
                        $tuVungData = $groupedData->get('tuVung', collect())->first();
                    @endphp
                    
                    @if(!$tuVungData)
                        <div class="rounded-lg bg-slate-50 p-8 text-center">
                            <p class="text-slate-500">Chưa có dữ liệu từ vựng cho bài này</p>
                        </div>
                    @else
                        @php 
                            $content = $tuVungData->content;
                            $words = is_array($content) ? $content : [];
                        @endphp
                        @if(!empty($words))
                            <div class="mt-4">
                                <!-- Desktop Table View -->
                                <div class="hidden overflow-x-auto rounded-xl border border-slate-200 md:block">
                                    <table class="min-w-full divide-y divide-slate-200">
                                        <thead class="bg-red-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Từ vựng</th>
                                                <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Nghĩa</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 bg-white">
                                            @foreach($words as $word)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-4">
                                                        <span class="japanese-text text-lg font-semibold">{{ $word['tu'] ?? '' }}</span>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <span class="text-slate-900">{{ $word['nghia'] ?? '' }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Mobile Card View -->
                                <div class="space-y-3 md:hidden">
                                    @foreach($words as $word)
                                        <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                            <div class="japanese-text mb-1 text-lg font-black text-slate-950">
                                                {{ $word['tu'] ?? '' }}
                                            </div>
                                            <div class="text-slate-700">
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
                        <div class="rounded-lg bg-slate-50 p-8 text-center">
                            <p class="text-slate-500">Chưa có dữ liệu ngữ pháp cho bài này</p>
                        </div>
                    @else
                        @php 
                            $content = $nguPhapData->content;
                            $grammarPoints = is_array($content) ? $content : [];
                        @endphp
                        @if(!empty($grammarPoints))
                            <div class="space-y-6">
                                @foreach($grammarPoints as $grammar)
                                    <div class="rounded-r-lg border-l-4 border-red-600 bg-slate-50 py-4 pl-4 pr-3 sm:pl-6">
                                        <h4 class="mb-2 break-words text-lg font-black text-slate-950 sm:text-xl">{{ $grammar['particle'] ?? '' }}</h4>
                                        
                                        @if(isset($grammar['explanation']))
                                            <p class="mb-4 text-sm leading-6 text-slate-700 sm:text-base">{{ $grammar['explanation'] }}</p>
                                        @endif
                                        
                                        @if(isset($grammar['examples']) && is_array($grammar['examples']))
                                            <div class="space-y-3">
                                                <h5 class="mb-2 font-black text-slate-700">Ví dụ</h5>
                                                @foreach($grammar['examples'] as $example)
                                                    <div class="rounded-lg border border-slate-200 bg-white p-4">
                                                        <div class="japanese-text mb-2 text-base text-slate-950 sm:text-lg">{{ $example['japanese'] ?? '' }}</div>
                                                        <div class="text-sm text-slate-700 sm:text-base">{{ $example['vietnamese'] ?? '' }}</div>
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
                        <div class="rounded-lg bg-slate-50 p-8 text-center">
                            <p class="text-slate-500">Chưa có dữ liệu đọc hiểu cho bài này</p>
                        </div>
                    @else
                        <div class="space-y-8">
                            @foreach($docHieuData as $item)
                                @php $content = $item->content; @endphp
                                <div>
                                    @if(isset($content['passage']))
                                        <div class="mb-6 rounded-lg border border-blue-100 bg-blue-50 p-4 sm:p-6">
                                            <h4 class="mb-3 font-black text-slate-950">Đoạn văn</h4>
                                            <p class="japanese-text whitespace-pre-wrap text-base text-slate-800 sm:text-lg">{{ $content['passage'] }}</p>
                                        </div>
                                    @endif
                                    
                                    @if(isset($content['question']))
                                        <div class="mb-4">
                                            <h4 class="mb-3 font-black text-slate-950">Câu hỏi</h4>
                                            <p class="text-base font-semibold text-slate-800 sm:text-lg">{{ $content['question'] }}</p>
                                        </div>
                                    @endif
                                    
                                    @if(isset($content['options']) && is_array($content['options']))
                                        <div class="space-y-3 mb-4">
                                            @foreach($content['options'] as $index => $option)
                                                <div class="rounded-lg border p-4 {{ isset($content['correct_answer']) && $content['correct_answer'] == $index ? 'border-green-300 bg-green-50' : 'border-slate-200 bg-slate-50' }}">
                                                    <div class="flex items-start gap-3">
                                                        <span class="mt-1 font-bold text-slate-700">{{ $index + 1 }}.</span>
                                                        <div class="flex-1">
                                                            <div class="japanese-text mb-1 text-base text-slate-950 sm:text-lg">{{ $option['text'] ?? '' }}</div>
                                                            @if(isset($option['romaji']))
                                                                <div class="text-sm text-slate-500">({{ $option['romaji'] }})</div>
                                                            @endif
                                                            @if(isset($option['meaning']))
                                                                <div class="text-sm text-slate-700">{{ $option['meaning'] }}</div>
                                                            @endif
                                                        </div>
                                                        @if(isset($content['correct_answer']) && $content['correct_answer'] == $index)
                                                            <span class="shrink-0 rounded-full bg-green-600 px-2.5 py-1 text-xs font-bold text-white">Đúng</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    
                                    @if(isset($content['explanation']))
                                        <div class="mt-4 rounded-lg border border-yellow-200 bg-yellow-50 p-4">
                                            <h5 class="mb-2 font-black text-slate-950">Giải thích</h5>
                                            <p class="text-sm leading-6 text-slate-700 sm:text-base">{{ $content['explanation'] }}</p>
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
                        <div class="rounded-lg bg-slate-50 p-8 text-center">
                            <p class="text-slate-500">Chưa có dữ liệu nghe hiểu cho bài này</p>
                        </div>
                    @else
                        <div class="space-y-8">
                            @foreach($ngheHieuData as $item)
                                <div>
                                    <p class="text-slate-600">Phần nghe hiểu - cần audio để học</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
                </div>
            </div>
        </section>
    </main>

    @include('layouts.footer')

    <script>
        function showSection(sectionId, tabElement) {
            // Hide all sections
            document.querySelectorAll('.section-content').forEach(section => {
                section.classList.add('hidden');
                section.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.section-tab').forEach(tab => {
                tab.classList.remove('active', 'bg-red-600', 'text-white', 'shadow-sm');
                tab.classList.add('text-slate-600');
            });
            
            // Show selected section
            document.getElementById(sectionId).classList.remove('hidden');
            document.getElementById(sectionId).classList.add('active');
            
            // Add active class to clicked tab
            tabElement.classList.add('active', 'bg-red-600', 'text-white', 'shadow-sm');
            tabElement.classList.remove('text-slate-600');
        }
    </script>
</body>
</html>
