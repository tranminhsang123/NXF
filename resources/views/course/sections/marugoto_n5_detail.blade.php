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
                <a href="{{ route('course.section', ['level' => $level, 'sectionType' => 'marugoto_n5']) }}" class="inline-flex items-center text-sm font-bold text-slate-600 hover:text-slate-950">
                    <svg class="mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Marugoto N5
                </a>
                <p class="mt-5 text-sm font-bold text-red-600">{{ $item->bai }}</p>
                <h1 class="mt-1 break-words text-3xl font-black tracking-tight text-slate-950 sm:text-4xl">{{ $item->title }}</h1>
            </div>
        </section>

        <section class="py-6 sm:py-8">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
                @php $content = $item->content; @endphp
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 lg:p-8">
                
                <!-- Từ vựng Section -->
                @if(isset($content['tuVung']) && is_array($content['tuVung']))
                    <div class="mb-8">
                        <h2 class="mb-4 border-b-2 border-red-600 pb-2 text-xl font-black text-slate-950 sm:text-2xl">Từ vựng</h2>
                        
                        <!-- Desktop Table View -->
                        <div class="mb-4 hidden overflow-x-auto rounded-xl border border-slate-200 md:block">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-red-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Từ vựng</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Romaji</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold uppercase tracking-wider text-slate-700">Nghĩa</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    @foreach($content['tuVung'] as $word)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <span class="japanese-text text-lg font-semibold">{{ $word['tu'] ?? '' }}</span>
                                            </td>
                                            <td class="px-6 py-4 text-slate-600">
                                                {{ $word['romaji'] ?? '' }}
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
                            @foreach($content['tuVung'] as $word)
                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                    <div class="japanese-text mb-1 text-lg font-black text-slate-950">
                                        {{ $word['tu'] ?? '' }}
                                    </div>
                                    @if(isset($word['romaji']))
                                        <div class="mb-1 text-sm text-slate-500">({{ $word['romaji'] }})</div>
                                    @endif
                                    <div class="text-slate-700">
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
                        <h2 class="mb-4 border-b-2 border-red-600 pb-2 text-xl font-black text-slate-950 sm:text-2xl">Ngữ pháp</h2>
                        
                        <div class="space-y-6">
                            @foreach($content['nguPhap'] as $grammar)
                                <div class="rounded-r-lg border-l-4 border-red-600 bg-slate-50 py-4 pl-4 pr-3 sm:pl-6">
                                    <h3 class="mb-2 break-words text-lg font-black text-slate-950 sm:text-xl">{{ $grammar['particle'] ?? '' }}</h3>
                                    
                                    @if(isset($grammar['vietnamese_meaning']))
                                        <p class="mb-2 text-base italic text-slate-600 sm:text-lg">{{ $grammar['vietnamese_meaning'] }}</p>
                                    @endif
                                    
                                    @if(isset($grammar['explanation']))
                                        <p class="mb-4 text-sm leading-6 text-slate-700 sm:text-base">{{ $grammar['explanation'] }}</p>
                                    @endif
                                    
                                    @if(isset($grammar['examples']) && is_array($grammar['examples']))
                                        <div class="space-y-3">
                                            <h4 class="mb-2 text-base font-black text-slate-700 sm:text-lg">Ví dụ</h4>
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
                    </div>
                @endif
                </div>
            </div>
        </section>
    </main>

    @include('layouts.footer')
</body>
</html>
