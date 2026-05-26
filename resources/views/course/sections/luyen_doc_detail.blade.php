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
            line-height: 1.8;
            word-break: keep-all;
            overflow-wrap: break-word;
        }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <!-- Hero Section -->
    <section class="pt-24 pb-12 {{ $courseData['bgColor'] }}">
        <div class="container mx-auto max-w-7xl px-4 md:px-6">
            <div class="mb-8">
                <a href="{{ route('course.section', ['level' => $level, 'sectionType' => 'luyen_doc']) }}" class="inline-flex items-center text-gray-700 hover:text-gray-900 transition mb-6 group font-medium">
                    <svg class="w-5 h-5 mr-1 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Quay lại Luyện đọc</span>
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
                
                @if(isset($content['passage']))
                    <div class="mb-8 p-5 md:p-6 lg:p-8 bg-blue-50 rounded-lg border border-blue-200">
                        <h4 class="text-lg md:text-xl font-semibold text-gray-900 mb-4">Đoạn văn:</h4>
                        <p class="japanese-text text-base md:text-lg lg:text-xl text-gray-800 leading-relaxed whitespace-pre-wrap break-words">{{ $content['passage'] }}</p>
                    </div>
                @endif
                
                @if(isset($content['questions']) && is_array($content['questions']))
                    <div class="space-y-8">
                        @foreach($content['questions'] as $questionIndex => $question)
                            <div class="border-t border-gray-200 pt-8">
                                <div class="mb-6">
                                    <h4 class="text-lg md:text-xl font-semibold text-gray-900 mb-2">
                                        <span class="text-red-600">{{ $question['question_number'] ?? '' }}</span>: {{ $question['question'] ?? '' }}
                                    </h4>
                                </div>
                                
                                @if(isset($question['options']) && is_array($question['options']))
                                    <div class="space-y-4 mb-6">
                                        @foreach($question['options'] as $index => $option)
                                            <div class="p-4 md:p-5 rounded-lg border-2 transition {{ isset($question['correct_answer']) && $question['correct_answer'] == $index ? 'border-green-500 bg-green-50' : 'border-gray-200 bg-white hover:border-gray-300 hover:shadow-sm' }}">
                                                <div class="flex items-start gap-4">
                                                    <span class="font-bold text-gray-700 text-lg mt-0.5 flex-shrink-0">{{ chr(65 + $index) }}.</span>
                                                    <div class="flex-1 min-w-0">
                                                        <div class="japanese-text text-base md:text-lg mb-2 break-words">{{ $option['text'] ?? '' }}</div>
                                                        @if(isset($option['romaji']))
                                                            <div class="text-sm md:text-base text-gray-600 mb-1 italic">({{ $option['romaji'] }})</div>
                                                        @endif
                                                        @if(isset($option['meaning']))
                                                            <div class="text-sm md:text-base text-gray-700 font-medium">{{ $option['meaning'] }}</div>
                                                        @endif
                                                    </div>
                                                    @if(isset($question['correct_answer']) && $question['correct_answer'] == $index)
                                                        <span class="text-green-600 font-bold text-xl flex-shrink-0">✓</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                @if(isset($question['explanation']))
                                    <div class="mt-6 p-5 bg-yellow-50 rounded-lg border border-yellow-200">
                                        <h5 class="font-semibold text-gray-900 mb-2 text-base md:text-lg">Giải thích:</h5>
                                        <p class="text-gray-700 text-sm md:text-base leading-relaxed">{{ $question['explanation'] }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </section>

    @include('layouts.footer')
</body>
</html>

