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
        .japanese-font {
            font-family: 'Hiragino Sans', 'Noto Sans JP', sans-serif;
            font-size: 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            margin: 0 auto;
            padding: 0;
            text-align: center;
            letter-spacing: 0;
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
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    @include('layouts.header')
    
    <div class="pt-24 p-8 flex-1">
        <div class="container mx-auto max-w-7xl">
            <!-- Title -->
            <div class="text-center mb-12">
                <h1 class="text-5xl font-bold text-gray-900 mb-4">
                    Bảng chữ cái tiếng Nhật
                </h1>
                <p class="text-xl text-gray-600">
                    Học 3 bảng chữ cái cơ bản của tiếng Nhật
                </p>
            </div>
            
            <!-- Tab Buttons -->
            <div class="flex justify-center mb-12 gap-2 flex-wrap">
                <button onclick="showContent('hiragana')" class="px-5 py-2 rounded bg-red-600 text-white hover:bg-red-700 transition text-sm">
                    Hiragana
                </button>
                <button onclick="showContent('katakana')" class="px-5 py-2 rounded bg-yellow-500 text-white hover:bg-yellow-600 transition text-sm">
                    Katakana
                </button>
                <button onclick="showContent('romaji')" class="px-5 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 transition text-sm">
                    Romaji
                </button>
                <button onclick="showContent('kanji')" class="px-5 py-2 rounded bg-green-600 text-white hover:bg-green-700 transition text-sm">
                    Kanji
                </button>
            </div>
            
            <!-- Content Sections -->
            <div id="hiragana" class="content-section">
                <div class="bg-white rounded-3xl shadow-xl p-12">
                    
                    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Bảng chữ cái Hiragana</h2>
                    <div class="grid grid-cols-5 gap-3 max-w-2xl mx-auto">
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
                                <div class="bg-gray-50 p-4 rounded-lg text-center border border-gray-200 h-16"></div>
                            @else
                                @php $charData = $hiraganaData->get($char); @endphp
                                @if($charData)
                                    <div class="char-card bg-red-50 p-4 rounded-lg border border-red-200 hover:shadow-md transition-all duration-300 h-16 flex flex-col justify-center items-center"
                                         data-char="{{ $charData->character }}"
                                         data-type="kana"
                                         data-reading="{{ $charData->romaji }}">
                                        <div class="japanese-font text-red-700 mb-1 text-xl w-full flex justify-center items-center"><span>{{ $charData->character }}</span></div>
                                        <div class="text-xs font-medium text-gray-600 w-full flex justify-center items-center"><span>{{ $charData->romaji }}</span></div>
                                    </div>
                                @else
                                    <div class="bg-gray-50 p-4 rounded-lg text-center border border-gray-200 h-16"></div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div id="katakana" class="content-section hidden">
                <div class="bg-white rounded-3xl shadow-xl p-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Bảng chữ cái Katakana</h2>
                    <div class="grid grid-cols-5 gap-3 max-w-2xl mx-auto">
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
                                <div class="bg-gray-50 p-4 rounded-lg text-center border border-gray-200 h-16"></div>
                            @else
                                @php $charData = $katakanaData->get($char); @endphp
                                @if($charData)
                                    <div class="char-card bg-yellow-50 p-4 rounded-lg border border-yellow-200 hover:shadow-md transition-all duration-300 h-16 flex flex-col justify-center items-center"
                                         data-char="{{ $charData->character }}"
                                         data-type="kana"
                                         data-reading="{{ $charData->romaji }}">
                                        <div class="japanese-font text-yellow-700 mb-1 text-xl w-full flex justify-center items-center"><span>{{ $charData->character }}</span></div>
                                        <div class="text-xs font-medium text-gray-600 w-full flex justify-center items-center"><span>{{ $charData->romaji }}</span></div>
                                    </div>
                                @else
                                    <div class="bg-gray-50 p-4 rounded-lg text-center border border-gray-200 h-16"></div>
                                @endif
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
            
            <div id="romaji" class="content-section hidden">
                <div class="bg-white rounded-3xl shadow-xl p-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Bảng chữ cái Romaji</h2>
                    
                    <!-- Seion (Âm cơ bản) -->
                    <div class="mb-12">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Seion (Âm cơ bản)</h3>
                        <div class="grid grid-cols-5 gap-3 max-w-2xl mx-auto">
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
                                    <div class="bg-gray-50 p-4 rounded-lg text-center border border-gray-200 h-16"></div>
                                @else
                                    @php $charData = $seionData->get($char); @endphp
                                    @if($charData)
                                        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 hover:shadow-md transition-all duration-300 h-16 flex flex-col justify-center items-center">
                                            <div class="text-lg font-bold text-blue-700 w-full flex justify-center items-center"><span>{{ $charData->character }}</span></div>
                                        </div>
                                    @else
                                        <div class="bg-gray-50 p-4 rounded-lg text-center border border-gray-200 h-16"></div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Dakuon (Âm đục) -->
                    <div class="mb-12">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Dakuon (Âm đục)</h3>
                        <div class="grid grid-cols-5 gap-3 max-w-2xl mx-auto">
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
                                    <div class="bg-gray-50 p-4 rounded-lg text-center border border-gray-200 h-16"></div>
                                @else
                                    @php $charData = $dakuonData->get($char); @endphp
                                    @if($charData)
                                        <div class="bg-green-50 p-4 rounded-lg border border-green-200 hover:shadow-md transition-all duration-300 h-16 flex flex-col justify-center items-center">
                                            <div class="text-lg font-bold text-green-700 w-full flex justify-center items-center"><span>{{ $charData->character }}</span></div>
                                        </div>
                                    @else
                                        <div class="bg-gray-50 p-4 rounded-lg text-center border border-gray-200 h-16"></div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Yōon (Âm ghép) -->
                    <div class="mb-12">
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Yōon (Âm ghép)</h3>
                        <div class="grid grid-cols-5 gap-3 max-w-4xl mx-auto">
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
                                    <div class="bg-gray-50 p-4 rounded-lg text-center border border-gray-200 h-16"></div>
                                @else
                                    @php $charData = $yoonData->get($char); @endphp
                                    @if($charData)
                                        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200 hover:shadow-md transition-all duration-300 h-16 flex flex-col justify-center items-center">
                                            <div class="text-lg font-bold text-purple-700 w-full flex justify-center items-center"><span>{{ $charData->character }}</span></div>
                                        </div>
                                    @else
                                        <div class="bg-gray-50 p-4 rounded-lg text-center border border-gray-200 h-16"></div>
                                    @endif
                                @endif
                        @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <div id="kanji" class="content-section hidden">
                <div class="bg-white rounded-3xl shadow-xl p-12">
                    <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Chữ Kanji</h2>
                    <!-- Bộ chọn cấp độ (không render Kanji cho đến khi chọn) -->
                    <div class="flex justify-center mb-8">
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
    <div id="charModal" class="fixed inset-0 modal-backdrop hidden z-50 items-center justify-center px-4">
        <div class="bg-white rounded-2xl max-w-md md:max-w-xl w-full p-6 md:p-8 relative shadow-2xl">
            <button type="button"
                    id="closeCharModal"
                    class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                ✕
            </button>

            <!-- Nút qua lại -->
            <button type="button" id="prevCharBtn"
                    class="absolute left-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 hover:text-gray-900 transition disabled:opacity-40 disabled:cursor-not-allowed"
                    title="Chữ trước">
                ←
            </button>
            <button type="button" id="nextCharBtn"
                    class="absolute right-2 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-600 hover:text-gray-900 transition disabled:opacity-40 disabled:cursor-not-allowed"
                    title="Chữ sau">
                →
            </button>

            <div class="flex flex-col items-center text-center gap-4 md:gap-6">
                <div id="modalCharText"
                     class="japanese-font text-6xl md:text-7xl mt-2 mb-1 text-gray-900">
                </div>
                <div id="modalReading"
                     class="text-sm text-gray-500 mb-1"></div>

                <button type="button"
                        id="playAudioBtn"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-red-600 text-white text-sm font-semibold shadow hover:bg-red-700 transition">
                    <span>Nghe phát âm</span>
                </button>

                <div class="mt-4 w-44 h-44 md:w-52 md:h-52 border border-gray-200 rounded-xl bg-gray-50 flex items-center justify-center overflow-hidden">
                    <div id="strokeContainer" class="w-full h-full flex items-center justify-center text-xs text-gray-400">
                        Đang tải thứ tự nét vẽ...
                    </div>
                </div>

                <div class="w-full mt-2 rounded-xl border border-gray-200 bg-gray-50 p-4">
                    <div class="flex items-center justify-between gap-3 mb-3">
                        <div class="text-left">
                            <p class="text-sm font-bold text-gray-900">Luyện viết tay</p>
                            <p class="text-xs text-gray-500">Vẽ trong khung rồi bấm chấm điểm.</p>
                        </div>
                        <span id="handwritingExpected" class="text-xs font-semibold text-gray-500"></span>
                    </div>
                    <canvas id="handwritingCanvas" width="280" height="280" class="drawing-canvas mx-auto block h-56 w-56 rounded-xl border border-gray-300 bg-white"></canvas>
                    <div class="mt-3 flex justify-center gap-2">
                        <button type="button" id="clearHandwriting" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-white">Xóa</button>
                        <button type="button" id="scoreHandwriting" class="rounded-lg bg-green-600 px-3 py-2 text-xs font-semibold text-white hover:bg-green-700">Chấm điểm</button>
                    </div>
                    <p id="handwritingResult" class="mt-3 min-h-5 text-sm font-semibold text-gray-700"></p>
                </div>

                <a href="{{ route('minna.index') }}"
                   class="mt-1 inline-flex items-center gap-1 text-xs text-red-600 hover:text-red-700 underline">
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

            let html = '<div class="grid grid-cols-4 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 gap-3 sm:gap-4">';
            for (const item of slice) {
                const reading = (item.on_reading || '') + (item.kun_reading ? ' ・ ' + item.kun_reading : '');
                html += `
                <div class="char-card p-3 sm:p-4 rounded-lg border hover:shadow-sm min-h-[84px] sm:min-h-[96px] flex flex-col justify-center items-center ${styles.card}"
                     data-char="${item.character}"
                     data-type="kanji"
                     data-reading="${reading.replace(/"/g, '&quot;')}"
                     data-meaning="${(item.meaning || '').replace(/"/g, '&quot;')}">
                    <div class="japanese-font text-2xl sm:text-3xl mb-1 ${styles.text} w-full flex justify-center items-center"><span>${item.character}</span></div>
                    <div class="text-[11px] sm:text-xs text-gray-600 kanji-desc text-center w-full">${item.meaning ?? ''}</div>
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
            handwritingResult.className = 'mt-3 min-h-5 text-sm font-semibold text-gray-700';
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
            const strokeContainerParent = strokeContainer.closest('.mt-4');
            
            if (type === 'kanji') {
                // Kanji: ẩn hoàn toàn phần hiển thị GIF
                if (strokeContainerParent) {
                    strokeContainerParent.style.display = 'none';
                }
            } else {
                // Hiển thị lại container cho Kana
                if (strokeContainerParent) {
                    strokeContainerParent.style.display = 'block';
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
                        strokeContainer.innerHTML = '<span class="text-[11px] text-gray-400 px-3 text-center">Chưa có GIF nét vẽ cho chữ này.</span>';
                    };
                    strokeContainer.appendChild(img);
                } else {
                    strokeContainer.innerHTML = '<span class="text-[11px] text-gray-400 px-3 text-center">Chưa có GIF nét vẽ cho chữ này.</span>';
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
