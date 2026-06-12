<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng chữ cái tiếng Nhật</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        .japanese-font {
            font-family: 'Hiragino Sans', 'Noto Sans JP', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin: 0 auto;
            padding: 0;
            text-align: center;
            letter-spacing: 0;
            line-height: 1;
        }
        .kana-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 0.375rem;
        }
        .kana-cell,
        .kana-empty {
            min-height: 3.15rem;
            border-radius: 0.5rem;
        }
        .kana-cell {
            padding: 0.35rem 0.2rem;
        }
        .kana-char {
            font-size: clamp(1.35rem, 7vw, 1.95rem);
        }
        .kana-reading {
            font-size: 0.68rem;
            line-height: 1;
        }
        .romaji-cell {
            min-height: 2.8rem;
            padding: 0.35rem 0.25rem;
        }
        .romaji-text {
            font-size: clamp(0.86rem, 3.8vw, 1.05rem);
            line-height: 1.1;
        }
        /* Giới hạn mô tả tiếng Việt trong thẻ, tránh tràn khung */
        .kanji-desc {
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3; /* cho mobile hiển thị tối đa 3 dòng */
            -webkit-box-orient: vertical;
            line-height: 1.1;
            word-break: break-word;
            white-space: normal;
        }
        .char-card {
            cursor: pointer;
        }
        .char-card:active {
            transform: scale(0.97);
        }
        .modal-backdrop {
            background: rgba(0, 0, 0, 0.6);
        }
        .drawing-canvas {
            touch-action: none; /* giúp vẽ trên mobile không bị cuộn trang */
        }
        .stroke-preview-size {
            width: min(7.75rem, 32vw);
            height: min(7.75rem, 32vw);
        }
        .handwriting-box-size {
            width: min(10rem, calc(100vw - 4rem));
            height: min(10rem, calc(100vw - 4rem));
        }
        .alphabet-modal-shell {
            max-height: 100svh;
            max-height: 100dvh;
        }
        .alphabet-modal-scroll {
            padding-bottom: calc(env(safe-area-inset-bottom, 0px) + 5rem);
        }
        @media (max-height: 740px) {
            .stroke-preview-size {
                width: 6.5rem;
                height: 6.5rem;
            }
            .handwriting-box-size {
                width: 9rem;
                height: 9rem;
            }
        }
        @media (min-width: 640px) {
            .kana-grid {
                gap: 0.75rem;
            }
            .kana-cell,
            .kana-empty {
                min-height: 4rem;
            }
            .kana-cell {
                padding: 0.75rem;
            }
            .kana-char {
                font-size: 2rem;
            }
            .kana-reading {
                font-size: 0.75rem;
            }
            .romaji-cell {
                min-height: 4rem;
                padding: 0.75rem;
            }
            .romaji-text {
                font-size: 1.125rem;
            }
            .stroke-preview-size {
                width: 11rem;
                height: 11rem;
            }
            .handwriting-box-size {
                width: 14rem;
                height: 14rem;
            }
            .alphabet-modal-shell {
                max-height: calc(100dvh - 2rem);
            }
            .alphabet-modal-scroll {
                padding-bottom: 1.25rem;
            }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex flex-col text-slate-900">
    @include('layouts.header')
    
    <div class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
        <div class="container mx-auto max-w-7xl">
            <!-- Title -->
            <div class="mb-6 text-center sm:mb-8">
                <h1 class="mb-3 text-2xl font-black tracking-tight text-slate-950 sm:text-4xl">
                    Bảng chữ cái tiếng Nhật
                </h1>
                <p class="text-sm leading-6 text-slate-600 sm:text-base">
                    Học 3 bảng chữ cái cơ bản của tiếng Nhật
                </p>
            </div>
            
            <!-- Tab Buttons -->
            <div class="-mx-4 mb-6 flex justify-start gap-2 overflow-x-auto px-4 pb-1 sm:mx-0 sm:mb-8 sm:flex-wrap sm:justify-center sm:px-0">
                <button onclick="showContent('hiragana')" class="shrink-0 rounded-lg bg-red-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-red-700">
                    Hiragana
                </button>
                <button onclick="showContent('katakana')" class="shrink-0 rounded-lg bg-yellow-500 px-4 py-2 text-sm font-bold text-white transition hover:bg-yellow-600">
                    Katakana
                </button>
                <button onclick="showContent('romaji')" class="shrink-0 rounded-lg bg-blue-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-blue-700">
                    Romaji
                </button>
                <button onclick="showContent('kanji')" class="shrink-0 rounded-lg bg-green-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-green-700">
                    Kanji
                </button>
            </div>
            
            <!-- Content Sections -->
            <div id="hiragana" class="content-section">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 lg:p-8">
                    
                    <h2 class="mb-5 text-center text-2xl font-black text-slate-950 sm:text-3xl">Bảng chữ cái Hiragana</h2>
                    <div class="kana-grid mx-auto max-w-2xl">
                        @php
                            $hiraganaOrder = [
                                'あ', 'い', 'う', 'え', 'お',
                                'か', 'き', 'く', 'け', 'こ',
                                'さ', 'し', 'す', 'せ', 'そ',
                                'た', 'ち', 'つ', 'て', 'と',
                                'な', 'に', 'ぬ', 'ね', 'の',
                                'は', 'ひ', 'ふ', 'へ', 'ほ',
                                'ま', 'み', 'む', 'め', 'も',
                                'や', '', 'ゆ', '', 'よ',
                                'ら', 'り', 'る', 'れ', 'ろ',
                                'わ', 'を', '', '', 'ん'
                            ];
                            $hiraganaData = $hiragana->keyBy('character');
                        @endphp
                        @foreach($hiraganaOrder as $char)
                            @if($char === '')
                                <div class="kana-empty border border-slate-100 bg-slate-50"></div>
                            @else
                                @php $charData = $hiraganaData->get($char); @endphp
                                @if($charData)
                                    <div class="char-card kana-cell flex flex-col items-center justify-center border border-red-200 bg-red-50 transition-all duration-300 hover:shadow-md"
                                         data-char="{{ $charData->character }}"
                                         data-type="kana"
                                         data-reading="{{ $charData->romaji }}">
                                        <div class="japanese-font kana-char mb-1 text-red-700"><span>{{ $charData->character }}</span></div>
                                        <div class="kana-reading flex w-full items-center justify-center font-semibold text-slate-600"><span>{{ $charData->romaji }}</span></div>
                                    </div>
                                @else
                                    <div class="kana-empty border border-slate-100 bg-slate-50"></div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div id="katakana" class="content-section hidden">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 lg:p-8">
                    <h2 class="mb-5 text-center text-2xl font-black text-slate-950 sm:text-3xl">Bảng chữ cái Katakana</h2>
                    <div class="kana-grid mx-auto max-w-2xl">
                        @php
                            $katakanaOrder = [
                                'ア', 'イ', 'ウ', 'エ', 'オ',
                                'カ', 'キ', 'ク', 'ケ', 'コ',
                                'サ', 'シ', 'ス', 'セ', 'ソ',
                                'タ', 'チ', 'ツ', 'テ', 'ト',
                                'ナ', 'ニ', 'ヌ', 'ネ', 'ノ',
                                'ハ', 'ヒ', 'フ', 'ヘ', 'ホ',
                                'マ', 'ミ', 'ム', 'メ', 'モ',
                                'ヤ', '', 'ユ', '', 'ヨ',
                                'ラ', 'リ', 'ル', 'レ', 'ロ',
                                'ワ', 'ヲ', '', '', 'ン'
                            ];
                            $katakanaData = $katakana->keyBy('character');
                        @endphp
                        @foreach($katakanaOrder as $char)
                            @if($char === '')
                                <div class="kana-empty border border-slate-100 bg-slate-50"></div>
                            @else
                                @php $charData = $katakanaData->get($char); @endphp
                                @if($charData)
                                    <div class="char-card kana-cell flex flex-col items-center justify-center border border-yellow-200 bg-yellow-50 transition-all duration-300 hover:shadow-md"
                                         data-char="{{ $charData->character }}"
                                         data-type="kana"
                                         data-reading="{{ $charData->romaji }}">
                                        <div class="japanese-font kana-char mb-1 text-yellow-700"><span>{{ $charData->character }}</span></div>
                                        <div class="kana-reading flex w-full items-center justify-center font-semibold text-slate-600"><span>{{ $charData->romaji }}</span></div>
                                    </div>
                                @else
                                    <div class="kana-empty border border-slate-100 bg-slate-50"></div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div id="romaji" class="content-section hidden">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 lg:p-8">
                    <h2 class="mb-5 text-center text-2xl font-black text-slate-950 sm:text-3xl">Bảng chữ cái Romaji</h2>
                    
                    <!-- Seion (Âm cơ bản) -->
                    <div class="mb-8">
                        <h3 class="mb-4 text-center text-xl font-black text-slate-800">Seion (Âm cơ bản)</h3>
                        <div class="kana-grid mx-auto max-w-2xl">
                            @php
                                $seionOrder = [
                                    'a', 'i', 'u', 'e', 'o',
                                    'ka', 'ki', 'ku', 'ke', 'ko',
                                    'sa', 'shi', 'su', 'se', 'so',
                                    'ta', 'chi', 'tsu', 'te', 'to',
                                    'na', 'ni', 'nu', 'ne', 'no',
                                    'ha', 'hi', 'fu', 'he', 'ho',
                                    'ma', 'mi', 'mu', 'me', 'mo',
                                    'ya', '', 'yu', '', 'yo',
                                    'ra', 'ri', 'ru', 're', 'ro',
                                    'wa', 'wo', '', '', 'n'
                                ];
                                $seionData = $romaji->where('category', 'seion')->keyBy('character');
                            @endphp
                            @foreach($seionOrder as $char)
                                @if($char === '')
                                    <div class="kana-empty border border-slate-100 bg-slate-50"></div>
                                @else
                                    @php $charData = $seionData->get($char); @endphp
                                    @if($charData)
                                        <div class="romaji-cell flex flex-col items-center justify-center rounded-lg border border-blue-200 bg-blue-50 transition-all duration-300 hover:shadow-md">
                                            <div class="romaji-text flex w-full items-center justify-center font-black text-blue-700"><span>{{ $charData->character }}</span></div>
                                        </div>
                                    @else
                                        <div class="kana-empty border border-slate-100 bg-slate-50"></div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Dakuon (Âm đục) -->
                    <div class="mb-8">
                        <h3 class="mb-4 text-center text-xl font-black text-slate-800">Dakuon (Âm đục)</h3>
                        <div class="kana-grid mx-auto max-w-2xl">
                            @php
                                $dakuonOrder = [
                                    'ga', 'gi', 'gu', 'ge', 'go',
                                    'za', 'ji', 'zu', 'ze', 'zo',
                                    'da', 'ji', 'zu', 'de', 'do',
                                    'ba', 'bi', 'bu', 'be', 'bo',
                                    'pa', 'pi', 'pu', 'pe', 'po',
                                    '', '', '', '', '',
                                    '', '', '', '', '',
                                    '', '', '', '', '',
                                    '', '', '', '', '',
                                    '', '', '', '', ''
                                ];
                                $dakuonData = $romaji->where('category', 'dakuon')->keyBy('character');
                            @endphp
                            @foreach($dakuonOrder as $char)
                                @if($char === '')
                                    <div class="kana-empty border border-slate-100 bg-slate-50"></div>
                                @else
                                    @php $charData = $dakuonData->get($char); @endphp
                                    @if($charData)
                                        <div class="romaji-cell flex flex-col items-center justify-center rounded-lg border border-green-200 bg-green-50 transition-all duration-300 hover:shadow-md">
                                            <div class="romaji-text flex w-full items-center justify-center font-black text-green-700"><span>{{ $charData->character }}</span></div>
                                        </div>
                                    @else
                                        <div class="kana-empty border border-slate-100 bg-slate-50"></div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Yōon (Âm ghép) -->
                    <div class="mb-8">
                        <h3 class="mb-4 text-center text-xl font-black text-slate-800">Yōon (Âm ghép)</h3>
                        <div class="kana-grid mx-auto max-w-4xl">
                            @php
                                $yoonOrder = [
                                    'kya', 'kyu', 'kyo', '', '',
                                    'sha', 'shu', 'sho', '', '',
                                    'cha', 'chu', 'cho', '', '',
                                    'nya', 'nyu', 'nyo', '', '',
                                    'hya', 'hyu', 'hyo', '', '',
                                    'mya', 'myu', 'myo', '', '',
                                    'rya', 'ryu', 'ryo', '', '',
                                    'gya', 'gyu', 'gyo', '', '',
                                    'ja', 'ju', 'jo', '', '',
                                    'bya', 'byu', 'byo', '', '',
                                    'pya', 'pyu', 'pyo', '', ''
                                ];
                                $yoonData = $romaji->where('category', 'yoon')->keyBy('character');
                            @endphp
                            @foreach($yoonOrder as $char)
                                @if($char === '')
                                    <div class="kana-empty border border-slate-100 bg-slate-50"></div>
                                @else
                                    @php $charData = $yoonData->get($char); @endphp
                                    @if($charData)
                                        <div class="romaji-cell flex flex-col items-center justify-center rounded-lg border border-purple-200 bg-purple-50 transition-all duration-300 hover:shadow-md">
                                            <div class="romaji-text flex w-full items-center justify-center font-black text-purple-700"><span>{{ $charData->character }}</span></div>
                                        </div>
                                    @else
                                        <div class="kana-empty border border-slate-100 bg-slate-50"></div>
                                    @endif
                                @endif
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="kanji" class="content-section hidden">
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6 lg:p-8">
                    <h2 class="mb-5 text-center text-2xl font-black text-slate-950 sm:text-3xl">Chữ Kanji</h2>
                    <!-- Bộ chọn cấp độ (không render Kanji cho đến khi chọn) -->
                    <div class="mb-6 flex justify-center">
                        <div class="inline-flex rounded-full bg-gray-100 p-1 shadow-inner">
                            <button type="button" data-level="N5" class="kanji-pill px-5 py-2 text-sm font-semibold rounded-full text-gray-700 hover:bg-white">N5</button>
                            <button type="button" data-level="N4" class="kanji-pill px-5 py-2 text-sm font-semibold rounded-full text-gray-700 hover:bg-white">N4</button>
                            <button type="button" data-level="N3" class="kanji-pill px-5 py-2 text-sm font-semibold rounded-full text-gray-700 hover:bg-white">N3</button>
                        </div>
                    </div>

                    <!-- Kết quả -->
                    <div id="kanjiResult" class="text-center text-gray-500">
                        <p class="text-lg">Chọn cấp độ để hiển thị Kanji</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modal luyện viết & phát âm -->
    <div id="charModal" class="fixed inset-0 modal-backdrop hidden z-50 items-end justify-center p-0 sm:items-center sm:p-4">
        <div class="alphabet-modal-shell relative flex w-full flex-col overflow-hidden rounded-t-2xl bg-white shadow-2xl sm:max-w-xl sm:rounded-2xl">
            <div class="flex shrink-0 items-center justify-between gap-2 border-b border-slate-200 px-3 py-2 sm:px-4 sm:py-2.5">
                <button type="button" id="prevCharBtn"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-200 hover:text-slate-950 disabled:cursor-not-allowed disabled:opacity-40 sm:h-10 sm:w-10"
                        title="Chữ trước"
                        aria-label="Chữ trước">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <p class="min-w-0 flex-1 truncate text-center text-sm font-black text-slate-950">Luyện chữ</p>

                <button type="button" id="nextCharBtn"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-600 transition hover:bg-slate-200 hover:text-slate-950 disabled:cursor-not-allowed disabled:opacity-40 sm:h-10 sm:w-10"
                        title="Chữ sau"
                        aria-label="Chữ sau">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <button type="button"
                        id="closeCharModal"
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 sm:h-10 sm:w-10"
                        aria-label="Đóng">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18 18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="alphabet-modal-scroll overflow-y-auto px-4 py-3 sm:px-6 sm:py-5">
                <div class="grid grid-cols-[minmax(0,1fr)_minmax(6.5rem,8rem)] items-center gap-3 sm:grid-cols-[minmax(0,1fr)_11rem] sm:gap-4">
                    <div class="flex flex-col items-center text-center">
                        <div id="modalCharText"
                             class="japanese-font text-4xl text-slate-950 sm:text-6xl">
                        </div>
                        <div id="modalReading"
                             class="mt-1 text-xs font-semibold text-slate-500 sm:text-sm"></div>

                        <button type="button"
                                id="playAudioBtn"
                                class="mt-3 inline-flex items-center gap-2 rounded-full bg-red-600 px-4 py-2 text-xs font-bold text-white shadow transition hover:bg-red-700 sm:mt-4 sm:px-5 sm:text-sm">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5 6 9H3v6h3l5 4V5Z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.5 8.5a5 5 0 0 1 0 7"></path>
                            </svg>
                            <span>Nghe</span>
                        </button>
                    </div>

                    <div id="strokePanel" class="mx-auto flex stroke-preview-size items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                        <div id="strokeContainer" class="flex h-full w-full items-center justify-center text-center text-xs text-slate-400">
                            Đang tải thứ tự nét vẽ...
                        </div>
                    </div>
                </div>

                <div class="mt-3 rounded-xl border border-slate-200 bg-slate-50 p-3 sm:mt-4 sm:p-4">
                    <div class="mb-2 flex items-start justify-between gap-3 sm:mb-3">
                        <div class="min-w-0 text-left">
                            <p class="text-sm font-black text-slate-950">Luyện viết tay</p>
                            <p class="text-xs leading-5 text-slate-500">Vẽ trong khung rồi bấm chấm điểm.</p>
                        </div>
                        <span id="handwritingExpected" class="shrink-0 text-xs font-bold text-slate-500"></span>
                    </div>
                    <canvas id="handwritingCanvas" width="280" height="280" class="drawing-canvas handwriting-box-size mx-auto block rounded-xl border border-slate-300 bg-white"></canvas>
                    <div class="mt-3 flex justify-center gap-2">
                        <button type="button" id="clearHandwriting" class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs font-bold text-slate-700 hover:bg-slate-50">Xóa</button>
                        <button type="button" id="scoreHandwriting" class="rounded-lg bg-green-600 px-3 py-2 text-xs font-bold text-white hover:bg-green-700">Chấm điểm</button>
                    </div>
                    <p id="handwritingResult" class="mt-3 min-h-5 text-sm font-semibold text-slate-700"></p>
                </div>

                <a href="{{ route('minna.index') }}"
                   class="mx-auto mt-4 inline-flex w-full items-center justify-center gap-1 text-xs font-semibold text-red-600 underline hover:text-red-700">
                    <span>Xem Hán tự trong Minna no Nihongo</span>
                </a>
            </div>
        </div>
    </div>
    
    @include('layouts.footer')
    
    <script>
        function showContent(type) {
            // Hide all content
            document.querySelectorAll('.content-section').forEach(section => {
                section.classList.add('hidden');
            });
            
            // Show selected content
            document.getElementById(type).classList.remove('hidden');
        }

        // Danh sách ký tự có thứ tự để điều hướng prev/next (Hiragana, Katakana)
        const HIRAGANA_ORDER = @json(array_values(array_filter($hiraganaOrder ?? [])));
        const KATAKANA_ORDER = @json(array_values(array_filter($katakanaOrder ?? [])));
        const HIRAGANA_MAP = @json($hiragana->keyBy('character')->map(fn($a) => $a->romaji)->toArray());
        const KATAKANA_MAP = @json($katakana->keyBy('character')->map(fn($a) => $a->romaji)->toArray());

        // Dữ liệu Kanji dưới dạng JSON, chỉ dùng khi người dùng chọn cấp
        // Ở đây truyền thẳng model sang JS, sau đó dùng các field: character, meaning, on_reading, kun_reading
        const KANJI = {
            N5: @json(isset($kanjiN5) ? $kanjiN5 : []),
            N4: @json(isset($kanjiN4) ? $kanjiN4 : []),
            N3: @json(isset($kanjiN3) ? $kanjiN3 : []),
        };

        const levelStyles = {
            N5: { card:'bg-orange-50 border-orange-200', text:'text-orange-700' },
            N4: { card:'bg-purple-50 border-purple-200', text:'text-purple-700' },
            N3: { card:'bg-indigo-50 border-indigo-200', text:'text-indigo-700' },
        };

        const pageSize = 9999; // Không phân trang nữa
        let currentLevel = null;
        let currentPage = 1;

        function renderKanji(level, page = 1) {
            currentLevel = level;
            const data = KANJI[level] || [];
            const styles = levelStyles[level];
            const result = document.getElementById('kanjiResult');

            if (!data.length) {
                result.innerHTML = '<div class="text-gray-500">Chưa có dữ liệu cho '+level+'</div>';
                return;
            }

            const slice = data; // hiển thị toàn bộ

            let html = '<div class="grid grid-cols-3 gap-2 sm:grid-cols-4 sm:gap-3 md:grid-cols-5 lg:grid-cols-6">';
            for (const item of slice) {
                const reading = (item.on_reading || '') + (item.kun_reading ? ' ・ ' + item.kun_reading : '');
                html += `
                <div class="char-card flex min-h-[74px] flex-col items-center justify-center rounded-lg border p-2 hover:shadow-sm sm:min-h-[92px] sm:p-3 ${styles.card}"
                     data-char="${item.character}"
                     data-type="kanji"
                     data-reading="${reading.replace(/"/g, '&quot;')}"
                     data-meaning="${(item.meaning || '').replace(/"/g, '&quot;')}">
                    <div class="japanese-font mb-1 text-2xl sm:text-3xl ${styles.text}"><span>${item.character}</span></div>
                    <div class="kanji-desc w-full text-center text-[10px] text-slate-600 sm:text-xs">${item.meaning ?? ''}</div>
                </div>`;
            }
            html += '</div>';
            result.innerHTML = html;

            // Không tạo phân trang nữa
        }

        document.addEventListener('click', (e)=>{
            const pill = e.target.closest('.kanji-pill');
            if (!pill) return;
            const level = pill.getAttribute('data-level');

            // Active style nhẹ
            document.querySelectorAll('.kanji-pill').forEach(btn=>btn.classList.remove('bg-white','shadow'));
            pill.classList.add('bg-white','shadow');

            renderKanji(level, 1);
        });

        // Modal luyện viết & phát âm
        const modal = document.getElementById('charModal');
        const modalCharText = document.getElementById('modalCharText');
        const modalReading = document.getElementById('modalReading');
        const playAudioBtn = document.getElementById('playAudioBtn');
        const closeModalBtn = document.getElementById('closeCharModal');
        const strokePanel = document.getElementById('strokePanel');
        const strokeContainer = document.getElementById('strokeContainer');
        const handwritingCanvas = document.getElementById('handwritingCanvas');
        const clearHandwritingBtn = document.getElementById('clearHandwriting');
        const scoreHandwritingBtn = document.getElementById('scoreHandwriting');
        const handwritingResult = document.getElementById('handwritingResult');
        const handwritingExpected = document.getElementById('handwritingExpected');
        const handwritingCtx = handwritingCanvas ? handwritingCanvas.getContext('2d') : null;

        let currentChar = null;
        let currentReading = '';
        let currentType = null;
        let currentSection = null;  // 'hiragana' | 'katakana' | 'kanji'
        let currentIndex = -1;
        let handwritingStrokes = [];
        let activeStroke = null;

        function getCharList() {
            if (currentSection === 'hiragana') return HIRAGANA_ORDER || [];
            if (currentSection === 'katakana') return KATAKANA_ORDER || [];
            if (currentSection === 'kanji' && currentLevel) return (KANJI[currentLevel] || []).map(k => k.character);
            return [];
        }

        function getReadingForChar(char, section) {
            if (section === 'hiragana') return (HIRAGANA_MAP || {})[char] || '';
            if (section === 'katakana') return (KATAKANA_MAP || {})[char] || '';
            if (section === 'kanji' && currentLevel) {
                const k = (KANJI[currentLevel] || []).find(x => x.character === char);
                return k ? ((k.on_reading || '') + (k.kun_reading ? ' ・ ' + k.kun_reading : '')) : '';
            }
            return '';
        }

        function getMeaningForKanji(char) {
            if (currentSection !== 'kanji' || !currentLevel) return '';
            const k = (KANJI[currentLevel] || []).find(x => x.character === char);
            return k ? (k.meaning || '') : '';
        }

        function updateNavButtons() {
            const list = getCharList();
            const prevBtn = document.getElementById('prevCharBtn');
            const nextBtn = document.getElementById('nextCharBtn');
            if (!prevBtn || !nextBtn) return;
            const canNav = list.length > 0 && currentIndex >= 0;
            prevBtn.style.visibility = canNav ? 'visible' : 'hidden';
            nextBtn.style.visibility = canNav ? 'visible' : 'hidden';
            prevBtn.disabled = !canNav || currentIndex <= 0;
            nextBtn.disabled = !canNav || currentIndex >= list.length - 1;
        }

        function setupHandwritingCanvas() {
            if (!handwritingCtx) return;
            handwritingCtx.lineWidth = 8;
            handwritingCtx.lineCap = 'round';
            handwritingCtx.lineJoin = 'round';
            handwritingCtx.strokeStyle = '#111827';
        }

        function getHandwritingPoint(event) {
            const rect = handwritingCanvas.getBoundingClientRect();
            const scaleX = handwritingCanvas.width / rect.width;
            const scaleY = handwritingCanvas.height / rect.height;

            return {
                x: (event.clientX - rect.left) * scaleX,
                y: (event.clientY - rect.top) * scaleY,
            };
        }

        function startHandwritingStroke(event) {
            if (!handwritingCtx || !handwritingCanvas) return;

            event.preventDefault();
            setupHandwritingCanvas();

            const point = getHandwritingPoint(event);
            activeStroke = [point];
            handwritingStrokes.push(activeStroke);

            handwritingCtx.beginPath();
            handwritingCtx.moveTo(point.x, point.y);
            if (handwritingCanvas.setPointerCapture) {
                handwritingCanvas.setPointerCapture(event.pointerId);
            }
        }

        function moveHandwritingStroke(event) {
            if (!handwritingCtx || !activeStroke) return;

            event.preventDefault();
            const point = getHandwritingPoint(event);
            const previous = activeStroke[activeStroke.length - 1];

            if (previous && Math.hypot(point.x - previous.x, point.y - previous.y) < 1) {
                return;
            }

            activeStroke.push(point);
            handwritingCtx.lineTo(point.x, point.y);
            handwritingCtx.stroke();
        }

        function finishHandwritingStroke(event) {
            if (!activeStroke) return;
            if (event) event.preventDefault();
            activeStroke = null;
        }

        function getExpectedStrokeCount() {
            if (currentSection === 'kanji' && currentLevel) {
                const item = (KANJI[currentLevel] || []).find(k => k.character === currentChar);
                const count = Number(item?.stroke_count || 0);
                return count > 0 ? count : null;
            }

            if (currentSection === 'hiragana' || currentSection === 'katakana') {
                const reading = (currentReading || '').toLowerCase();
                const oneStroke = new Set(['i', 'u', 'ku', 'shi', 'su', 'tsu', 'te', 'no', 'hi', 'he', 'ru', 're', 'ro', 'wa', 'wo', 'n']);
                const threeStroke = new Set(['a', 'o', 'ka', 'ki', 'sa', 'se', 'ta', 'na', 'ha', 'ho', 'ma', 'mu', 'ya', 'yu', 'yo']);

                if (oneStroke.has(reading)) return 1;
                if (threeStroke.has(reading)) return 3;

                return 2;
            }

            return null;
        }

        function resetHandwriting() {
            if (!handwritingCtx || !handwritingCanvas) return;

            handwritingCtx.clearRect(0, 0, handwritingCanvas.width, handwritingCanvas.height);
            setupHandwritingCanvas();
            handwritingStrokes = [];
            activeStroke = null;

            const expected = getExpectedStrokeCount();
            handwritingExpected.textContent = expected ? `Gợi ý: ${expected} nét` : 'Vẽ tự do';
            handwritingResult.textContent = '';
            handwritingResult.className = 'mt-3 min-h-5 text-sm font-semibold text-slate-700';
        }

        function scoreHandwriting() {
            const strokes = handwritingStrokes.filter(stroke => stroke.length > 1);

            if (!handwritingCtx || strokes.length === 0) {
                handwritingResult.textContent = 'Hãy vẽ chữ trong khung trước khi chấm điểm.';
                handwritingResult.className = 'mt-3 min-h-5 text-sm font-semibold text-amber-700';
                return;
            }

            let pathLength = 0;
            let pointCount = 0;
            let minX = handwritingCanvas.width;
            let minY = handwritingCanvas.height;
            let maxX = 0;
            let maxY = 0;

            for (const stroke of strokes) {
                pointCount += stroke.length;

                for (let i = 0; i < stroke.length; i++) {
                    const point = stroke[i];
                    minX = Math.min(minX, point.x);
                    minY = Math.min(minY, point.y);
                    maxX = Math.max(maxX, point.x);
                    maxY = Math.max(maxY, point.y);

                    if (i > 0) {
                        const previous = stroke[i - 1];
                        pathLength += Math.hypot(point.x - previous.x, point.y - previous.y);
                    }
                }
            }

            const expected = getExpectedStrokeCount();
            const boxWidth = Math.max(1, maxX - minX);
            const boxHeight = Math.max(1, maxY - minY);
            const coverage = (boxWidth * boxHeight) / (handwritingCanvas.width * handwritingCanvas.height);
            let score = 100;

            if (expected) {
                score -= Math.min(45, Math.abs(strokes.length - expected) * 16);
            }

            if (pathLength < 70) score -= 25;
            if (pointCount < 8) score -= 20;
            if (coverage < 0.08) score -= 22;
            if (coverage > 0.75) score -= 10;

            score = Math.max(0, Math.min(100, Math.round(score)));

            const strokeText = expected ? `${strokes.length}/${expected} nét` : `${strokes.length} nét`;
            if (score >= 80) {
                handwritingResult.textContent = `Tốt: ${score}/100 (${strokeText}). Tiếp tục giữ kích thước chữ đều như vậy.`;
                handwritingResult.className = 'mt-3 min-h-5 text-sm font-semibold text-green-700';
            } else if (score >= 55) {
                handwritingResult.textContent = `Ổn: ${score}/100 (${strokeText}). Thử viết rõ nét hơn và phủ khung đều hơn.`;
                handwritingResult.className = 'mt-3 min-h-5 text-sm font-semibold text-amber-700';
            } else {
                handwritingResult.textContent = `Cần luyện thêm: ${score}/100 (${strokeText}). Xem thứ tự nét rồi viết lại chậm hơn.`;
                handwritingResult.className = 'mt-3 min-h-5 text-sm font-semibold text-red-700';
            }
        }

        function openCharModal(char, type, reading) {
            currentChar = char;
            currentReading = reading || '';
            currentType = type;

            // Xác định section và index cho điều hướng
            const code = (char || '').charCodeAt(0);
            if (type === 'kanji' && currentLevel) {
                currentSection = 'kanji';
                const list = (KANJI[currentLevel] || []).map(k => k.character);
                currentIndex = list.indexOf(char);
            } else if (code >= 0x3040 && code <= 0x309F) {
                currentSection = 'hiragana';
                currentIndex = (HIRAGANA_ORDER || []).indexOf(char);
            } else if (code >= 0x30A0 && code <= 0x30FF) {
                currentSection = 'katakana';
                currentIndex = (KATAKANA_ORDER || []).indexOf(char);
            } else {
                currentSection = null;
                currentIndex = -1;
            }

            modalCharText.textContent = char || '';
            modalReading.textContent = currentReading ? ('Cách đọc: ' + currentReading) : '';

            // Setup audio
            playAudioBtn.onclick = function () {
                if (!('speechSynthesis' in window)) {
                    alert('Trình duyệt của bạn không hỗ trợ phát âm (Speech Synthesis).');
                    return;
                }
                const utterance = new SpeechSynthesisUtterance(char);
                utterance.lang = 'ja-JP';
                utterance.rate = 0.9;
                window.speechSynthesis.cancel();
                window.speechSynthesis.speak(utterance);
            };

            // Hiển thị GIF thứ tự nét vẽ
            if (type === 'kanji') {
                // Kanji: ẩn hoàn toàn phần hiển thị GIF
                if (strokePanel) {
                    strokePanel.style.display = 'none';
                }
            } else {
                // Hiển thị lại container cho Kana
                if (strokePanel) {
                    strokePanel.style.display = 'flex';
                }
                
                strokeContainer.innerHTML = '';
                
                if (type === 'kana' && reading) {
                    // Hiragana/Katakana: sử dụng romaji để tìm file GIF
                    // Phân biệt Hiragana (あ-ん) và Katakana (ア-ン)
                    const romajiForFile = reading.toLowerCase();
                    let gifPath = '';
                    
                    // Kiểm tra xem là Hiragana hay Katakana
                    // Hiragana: U+3040-U+309F, Katakana: U+30A0-U+30FF
                    const charCode = char.charCodeAt(0);
                    if (charCode >= 0x3040 && charCode <= 0x309F) {
                        // Hiragana: anime-h-{romaji}2.gif
                        gifPath = '/images/gif/Hiragana/anime-h-' + romajiForFile + '2.gif';
                    } else if (charCode >= 0x30A0 && charCode <= 0x30FF) {
                        // Katakana: anime-k-{romaji}2.gif
                        gifPath = '/images/gif/Katakana/anime-k-' + romajiForFile + '2.gif';
                    } else {
                        // Mặc định thử Hiragana
                        gifPath = '/images/gif/Hiragana/anime-h-' + romajiForFile + '2.gif';
                    }
                    
                    const img = document.createElement('img');
                    img.src = gifPath;
                    img.alt = 'Thứ tự nét vẽ ' + char;
                    img.className = 'w-full h-full object-contain';
                    img.onerror = function () {
                        strokeContainer.innerHTML = '<span class="px-3 text-center text-[11px] text-slate-400">Chưa có GIF nét vẽ cho chữ này.</span>';
                    };
                    strokeContainer.appendChild(img);
                } else {
                    strokeContainer.innerHTML = '<span class="px-3 text-center text-[11px] text-slate-400">Chưa có GIF nét vẽ cho chữ này.</span>';
                }
            }

            updateNavButtons();
            resetHandwriting();
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeCharModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            if (window.speechSynthesis) {
                window.speechSynthesis.cancel();
            }
        }

        closeModalBtn.addEventListener('click', closeCharModal);
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                closeCharModal();
            }
        });

        if (handwritingCanvas) {
            handwritingCanvas.addEventListener('pointerdown', startHandwritingStroke);
            handwritingCanvas.addEventListener('pointermove', moveHandwritingStroke);
            handwritingCanvas.addEventListener('pointerup', finishHandwritingStroke);
            handwritingCanvas.addEventListener('pointercancel', finishHandwritingStroke);
            handwritingCanvas.addEventListener('pointerleave', finishHandwritingStroke);
        }

        if (clearHandwritingBtn) {
            clearHandwritingBtn.addEventListener('click', resetHandwriting);
        }

        if (scoreHandwritingBtn) {
            scoreHandwritingBtn.addEventListener('click', scoreHandwriting);
        }

        document.getElementById('prevCharBtn').addEventListener('click', function (e) {
            e.stopPropagation();
            const list = getCharList();
            if (currentIndex <= 0) return;
            const char = list[currentIndex - 1];
            const reading = currentSection === 'kanji' ? getReadingForChar(char, 'kanji') : getReadingForChar(char, currentSection);
            if (currentSection === 'kanji') {
                openCharModal(char, 'kanji', reading);
            } else {
                openCharModal(char, 'kana', reading);
            }
        });

        document.getElementById('nextCharBtn').addEventListener('click', function (e) {
            e.stopPropagation();
            const list = getCharList();
            if (currentIndex < 0 || currentIndex >= list.length - 1) return;
            const char = list[currentIndex + 1];
            const reading = currentSection === 'kanji' ? getReadingForChar(char, 'kanji') : getReadingForChar(char, currentSection);
            if (currentSection === 'kanji') {
                openCharModal(char, 'kanji', reading);
            } else {
                openCharModal(char, 'kana', reading);
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeCharModal();
            }
        });

        // Lắng nghe click trên bất kỳ .char-card nào
        document.addEventListener('click', function (e) {
            const card = e.target.closest('.char-card');
            if (!card) return;
            const char = card.getAttribute('data-char');
            const type = card.getAttribute('data-type');
            const reading = card.getAttribute('data-reading') || '';
            openCharModal(char, type, reading);
        });

        // Loại bỏ xử lý click phân trang
    </script>
</body>
</html>
