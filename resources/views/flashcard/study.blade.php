<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Flashcard{{ $srsMode ? ' · Ôn tập' : '' }} - {{ ($lesson->title ?? 'Ôn từ vựng') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        .jp { font-family: 'Hiragino Sans','Yu Gothic','Meiryo',sans-serif; }
        .card-wrap { perspective: 1000px; }
        .card { transition: transform 0.5s; transform-style: preserve-3d; cursor: pointer; min-height: 220px; }
        .card.flip { transform: rotateY(180deg); }
        .face { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 2rem; border-radius: 1rem; left: 0; top: 0; }
        .front { background: linear-gradient(135deg,#fef2f2,#fee2e2); border: 2px solid #fecaca; }
        .back { background: linear-gradient(135deg,#f0fdf4,#dcfce7); border: 2px solid #bbf7d0; transform: rotateY(180deg); }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="container mx-auto px-4 max-w-2xl py-24">
        <a href="{{ route('flashcard.index') }}" class="text-red-600 hover:text-red-700 text-sm mb-6 inline-block">← Chọn bài khác</a>

        <h1 class="text-xl font-bold text-gray-900 mb-2 text-center">
            @if(!empty($deckTitle))
                {{ $deckTitle }}
            @elseif(isset($lessons) && count($lessons) > 1)
                Ôn từ bài {{ min(array_map(fn($l) => $l->number, $lessons)) }} đến {{ max(array_map(fn($l) => $l->number, $lessons)) }}
            @elseif($lesson)
                Bài {{ str_pad($lesson->number, 2, '0', STR_PAD_LEFT) }} - {{ $lesson->title }}
            @else
                Ôn từ vựng
            @endif
        </h1>

        @if($srsMode && $srsStats)
            <p class="text-center text-xs text-gray-500 mb-4">
                Đến hạn: {{ $srsStats['due_count'] }} · Thẻ mới trong phiên: {{ $srsStats['new_count'] }}
                @if(($srsStats['total_in_scope'] ?? 0) > 0)
                    <span class="text-gray-400">· Tổng trong phạm vi: {{ $srsStats['total_in_scope'] }}</span>
                @endif
            </p>
        @endif

        <div class="flex flex-wrap items-center justify-center gap-3 mb-6">
            @if(!$srsMode)
                <div class="flex items-center gap-2">
                    <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['shuffle' => request()->query('shuffle') ? '0' : '1'])) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ request()->query('shuffle') ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        🔀 Xáo trộn
                    </a>
                    <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['reverse' => request()->query('reverse') ? '0' : '1'])) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ request()->query('reverse') ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        ↩️ Đảo thẻ
                    </a>
                </div>
            @else
                <div class="flex items-center gap-2">
                    <a href="{{ url()->current() }}?{{ http_build_query(array_merge(request()->query(), ['reverse' => request()->query('reverse') ? '0' : '1'])) }}"
                       class="px-3 py-1.5 text-sm rounded-lg {{ request()->query('reverse') ? 'bg-violet-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' }}">
                        ↩️ Đảo thẻ
                    </a>
                </div>
            @endif
            <button type="button" id="btn-speak" class="px-3 py-1.5 text-sm bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300" title="Phát âm">
                🔊 Phát âm
            </button>
        </div>

        <div id="study-area">
        <p class="text-center text-gray-500 text-sm mb-4">
            Thẻ <span id="idx">1</span>/<span id="total">{{ count($cards) }}</span>
            <span class="text-gray-400 text-xs block mt-1">Space: lật @if(!$srsMode)• ← →: chuyển thẻ @endif @if($srsMode)• Sau khi lật: chọn mức nhớ @endif</span>
        </p>

        <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
            <div id="progress-bar" class="{{ $srsMode ? 'bg-violet-600' : 'bg-red-600' }} h-2 rounded-full transition-all duration-300" style="width: {{ count($cards) ? (100 / count($cards)) : 0 }}%"></div>
        </div>

        <div class="card-wrap mb-6">
            <div class="card relative" id="card">
                @php
                    $reverse = request()->query('reverse', false);
                    $frontText = $reverse ? ($cards[0]['back'] ?? '') : ($cards[0]['front'] ?? '');
                    $backText = $reverse ? ($cards[0]['front'] ?? '') : ($cards[0]['back'] ?? '');
                @endphp
                <div class="face front">
                    <span class="jp text-3xl font-bold text-center" id="f">{{ $frontText }}</span>
                    @if(isset($cards[0]['lesson_number']))
                        <span class="text-xs text-gray-500 mt-2">Bài {{ $cards[0]['lesson_number'] }}</span>
                    @endif
                    <span class="text-xs text-gray-400 mt-2">Nhấn để lật</span>
                </div>
                <div class="face back">
                    <span class="jp text-2xl font-bold mb-2 text-center" id="bf">{{ $reverse ? ($cards[0]['back'] ?? '') : ($cards[0]['front'] ?? '') }}</span>
                    @if(isset($cards[0]['lesson_number']))
                        <span class="text-xs text-gray-500 mb-1">Bài {{ $cards[0]['lesson_number'] }}</span>
                    @endif
                    <span class="text-gray-700 text-center text-lg" id="bb">{{ $reverse ? ($cards[0]['front'] ?? '') : ($cards[0]['back'] ?? '') }}</span>
                </div>
            </div>
        </div>

        @if($srsMode)
            <div id="srs-actions" class="hidden mb-4 space-y-2">
                <p class="text-center text-xs text-gray-500">Bạn nhớ thế nào?</p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <button type="button" data-quality="0" class="srs-rate px-3 py-3 rounded-xl border-2 border-red-200 bg-red-50 text-red-900 text-sm font-medium hover:bg-red-100">Quên<br><span class="text-xs opacity-75">Ôn lại</span></button>
                    <button type="button" data-quality="2" class="srs-rate px-3 py-3 rounded-xl border-2 border-amber-200 bg-amber-50 text-amber-900 text-sm font-medium hover:bg-amber-100">Khó<br><span class="text-xs opacity-75">Cần ôn</span></button>
                    <button type="button" data-quality="4" class="srs-rate px-3 py-3 rounded-xl border-2 border-emerald-200 bg-emerald-50 text-emerald-900 text-sm font-medium hover:bg-emerald-100">Được<br><span class="text-xs opacity-75">Nhớ ổn</span></button>
                    <button type="button" data-quality="5" class="srs-rate px-3 py-3 rounded-xl border-2 border-violet-200 bg-violet-50 text-violet-900 text-sm font-medium hover:bg-violet-100">Dễ<br><span class="text-xs opacity-75">Rất nhớ</span></button>
                </div>
            </div>
            <p id="srs-hint" class="text-center text-xs text-gray-400 mb-4">Lật thẻ trước khi đánh giá.</p>
        @else
            <div class="flex gap-4 mb-4">
                <button id="prev" class="flex-1 py-3 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed font-medium" {{ count($cards) <= 1 ? 'disabled' : '' }}>← Trước</button>
                <button id="next" class="flex-1 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">Sau →</button>
            </div>
        @endif
        </div>

        <div id="session-result" class="hidden bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
            <div class="text-center mb-6">
                <div class="mx-auto mb-4 w-14 h-14 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-2xl font-bold">✓</div>
                <h2 class="text-2xl font-bold text-gray-900">Đã hoàn thành phiên ôn</h2>
                <p class="text-sm text-gray-500 mt-2">Kết quả này giúp bạn biết nhóm thẻ nào cần ôn lại tiếp.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                <div class="rounded-xl bg-red-50 border border-red-100 p-4 text-center">
                    <p class="text-xs font-semibold text-red-700">Quên</p>
                    <p id="result-again" class="text-2xl font-bold text-red-900 mt-1">0</p>
                </div>
                <div class="rounded-xl bg-amber-50 border border-amber-100 p-4 text-center">
                    <p class="text-xs font-semibold text-amber-700">Khó</p>
                    <p id="result-hard" class="text-2xl font-bold text-amber-900 mt-1">0</p>
                </div>
                <div class="rounded-xl bg-emerald-50 border border-emerald-100 p-4 text-center">
                    <p class="text-xs font-semibold text-emerald-700">Tốt</p>
                    <p id="result-good" class="text-2xl font-bold text-emerald-900 mt-1">0</p>
                </div>
                <div class="rounded-xl bg-violet-50 border border-violet-100 p-4 text-center">
                    <p class="text-xs font-semibold text-violet-700">Dễ</p>
                    <p id="result-easy" class="text-2xl font-bold text-violet-900 mt-1">0</p>
                </div>
            </div>

            <div class="rounded-xl bg-gray-50 border border-gray-200 p-4 mb-6">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-600">Tổng lượt đánh giá</span>
                    <span id="result-total" class="font-bold text-gray-900">0</span>
                </div>
                <div class="flex items-center justify-between text-sm mt-2">
                    <span class="text-gray-600">Số thẻ trong phiên</span>
                    <span id="result-card-count" class="font-bold text-gray-900">{{ count($cards) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                @auth
                    <a href="{{ route('user.dashboard') }}" class="px-4 py-3 rounded-xl bg-red-600 text-white text-center font-semibold hover:bg-red-700">Về dashboard</a>
                @else
                    <a href="{{ route('home') }}" class="px-4 py-3 rounded-xl bg-red-600 text-white text-center font-semibold hover:bg-red-700">Về trang chủ</a>
                @endauth
                <a href="{{ route('flashcard.index') }}" class="px-4 py-3 rounded-xl bg-gray-900 text-white text-center font-semibold hover:bg-gray-800">Chọn bài khác</a>
                <button type="button" id="study-restart" class="px-4 py-3 rounded-xl border border-gray-300 text-gray-700 font-semibold hover:bg-gray-50">Ôn lại phiên này</button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const srsMode = @json($srsMode);
            const reverse = {{ request()->query('reverse') ? 'true' : 'false' }};
            let cards = @json($cards);
            const reviewUrl = @json(route('flashcard.review'));
            const indexUrl = @json(route('flashcard.index'));
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            let N = cards.length;
            const initialCards = cards.slice();
            const initialCardCount = cards.length;
            let i = 0;
            const sessionStats = {
                again: 0,
                hard: 0,
                good: 0,
                easy: 0,
                total: 0,
            };

            const cardEl = document.getElementById('card');
            const studyArea = document.getElementById('study-area');
            const sessionResult = document.getElementById('session-result');
            const f = document.getElementById('f'), bf = document.getElementById('bf'), bb = document.getElementById('bb');
            const idxEl = document.getElementById('idx');
            const totalEl = document.getElementById('total');
            const progressBar = document.getElementById('progress-bar');
            const btnSpeak = document.getElementById('btn-speak');
            const srsActions = document.getElementById('srs-actions');
            const srsHint = document.getElementById('srs-hint');
            const restartButton = document.getElementById('study-restart');

            function getFront(c) { return reverse ? c.back : c.front; }
            function getBack(c) { return reverse ? c.front : c.back; }

            function recordQuality(q) {
                sessionStats.total++;
                if (q < 1) {
                    sessionStats.again++;
                } else if (q < 3) {
                    sessionStats.hard++;
                } else if (q < 5) {
                    sessionStats.good++;
                } else {
                    sessionStats.easy++;
                }
            }

            function updateResultText(id, value) {
                const el = document.getElementById(id);
                if (el) el.textContent = String(value);
            }

            function showResult() {
                updateResultText('result-again', sessionStats.again);
                updateResultText('result-hard', sessionStats.hard);
                updateResultText('result-good', sessionStats.good);
                updateResultText('result-easy', sessionStats.easy);
                updateResultText('result-total', sessionStats.total || initialCardCount);
                updateResultText('result-card-count', initialCardCount);

                studyArea?.classList.add('hidden');
                sessionResult?.classList.remove('hidden');
                btnSpeak?.classList.add('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }

            function completeNormalSession() {
                sessionStats.total = initialCardCount;
                showResult();
            }

            restartButton?.addEventListener('click', function () {
                cards = initialCards.slice();
                N = cards.length;
                i = 0;
                sessionStats.again = 0;
                sessionStats.hard = 0;
                sessionStats.good = 0;
                sessionStats.easy = 0;
                sessionStats.total = 0;
                sessionResult?.classList.add('hidden');
                studyArea?.classList.remove('hidden');
                btnSpeak?.classList.remove('hidden');
                go(0);
            });

            function go(n) {
                i = Math.max(0, Math.min(n, N - 1));
                const c = cards[i];
                f.textContent = getFront(c);
                bf.textContent = getFront(c);
                bb.textContent = getBack(c);
                cardEl.classList.remove('flip');
                idxEl.textContent = i + 1;
                totalEl.textContent = N;
                progressBar.style.width = (N ? ((i + 1) / N * 100) : 0) + '%';
                if (!srsMode) {
                    document.getElementById('prev').disabled = i === 0;
                    document.getElementById('next').textContent = i === N - 1 ? 'Xong' : 'Sau →';
                } else {
                    srsActions.classList.add('hidden');
                    srsHint.classList.remove('hidden');
                }
            }

            cardEl.onclick = function() {
                cardEl.classList.toggle('flip');
                if (srsMode && cardEl.classList.contains('flip')) {
                    srsActions.classList.remove('hidden');
                    srsHint.classList.add('hidden');
                }
            };

            if (!srsMode) {
                const prev = document.getElementById('prev');
                const next = document.getElementById('next');
                prev.onclick = () => go(i - 1);
                next.onclick = () => i < N - 1 ? go(i + 1) : completeNormalSession();
                document.onkeydown = function(e) {
                    if (e.key === ' ') { e.preventDefault(); cardEl.classList.toggle('flip'); return; }
                    if (e.key === 'ArrowLeft') { go(i - 1); return; }
                    if (e.key === 'ArrowRight') { i < N - 1 ? go(i + 1) : completeNormalSession(); }
                };
            } else {
                document.onkeydown = function(e) {
                    if (e.key === ' ') { e.preventDefault(); cardEl.classList.toggle('flip'); if (cardEl.classList.contains('flip')) { srsActions.classList.remove('hidden'); srsHint.classList.add('hidden'); } }
                };

                document.querySelectorAll('.srs-rate').forEach(function(btn) {
                    btn.onclick = function() {
                        if (!cardEl.classList.contains('flip')) return;
                        const q = parseInt(btn.getAttribute('data-quality'), 10);
                        const c = cards[i];
                        if (c.section_id === undefined || c.card_index === undefined) return;

                        document.querySelectorAll('.srs-rate').forEach(function(b) { b.disabled = true; });
                        fetch(reviewUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify({
                                minna_section_id: c.section_id,
                                card_index: c.card_index,
                                quality: q
                            })
                        }).then(function(r) {
                            if (!r.ok) throw new Error('review failed');
                            return r.json();
                        }).then(function() {
                            recordQuality(q);
                            if (q < 3) {
                                const cur = cards.splice(i, 1)[0];
                                cards.push(cur);
                            } else {
                                i++;
                            }
                            N = cards.length;
                            if (N === 0 || i >= N) {
                                showResult();
                                return;
                            }
                            go(i);
                        }).catch(function() {
                            alert('Không lưu được đánh giá. Thử lại.');
                        }).finally(function() {
                            document.querySelectorAll('.srs-rate').forEach(function(b) { b.disabled = false; });
                        });
                    };
                });
            }

            if (btnSpeak && ('speechSynthesis' in window)) {
                btnSpeak.onclick = function() {
                    const text = cardEl.classList.contains('flip') ? getBack(cards[i]) : getFront(cards[i]);
                    if (!text) return;
                    const u = new SpeechSynthesisUtterance(text);
                    u.lang = 'ja-JP';
                    u.rate = 0.9;
                    speechSynthesis.cancel();
                    speechSynthesis.speak(u);
                };
            } else if (btnSpeak) {
                btnSpeak.style.display = 'none';
            }

            go(0);
        })();
    </script>
</body>
</html>
