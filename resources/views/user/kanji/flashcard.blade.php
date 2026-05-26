<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flashcard Kanji {{ $level }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    <style>
        .jp { font-family: 'Hiragino Sans','Yu Gothic','Meiryo',sans-serif; }
        .card-wrap { perspective: 1000px; }
        .card { transition: transform 0.5s; transform-style: preserve-3d; cursor: pointer; min-height: 220px; }
        .card.flip { transform: rotateY(180deg); }
        .face { position: absolute; width: 100%; height: 100%; backface-visibility: hidden; display: flex; align-items: center; justify-content: center; flex-direction: column; padding: 2rem; border-radius: 1rem; left: 0; top: 0; }
        .front { background: linear-gradient(135deg,#f0fdf4,#dcfce7); border: 2px solid #bbf7d0; }
        .back { background: linear-gradient(135deg,#fef2f2,#fee2e2); border: 2px solid #fecaca; transform: rotateY(180deg); }
    </style>
</head>
<body class="bg-gray-50">
    @include('layouts.header')

    <div class="container mx-auto px-4 max-w-2xl py-24">
        <a href="{{ route('kanji.index') }}" class="text-red-600 hover:text-red-700 text-sm mb-6 inline-block">← Chọn cấp khác</a>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold text-gray-900">Flashcard Kanji {{ $level }}</h1>
            <a href="{{ route('kanji.list', $level) }}" class="text-sm text-gray-600 hover:text-gray-900">Xem danh sách</a>
        </div>

        <p class="text-center text-gray-500 text-sm mb-4">Thẻ <span id="idx">1</span>/{{ count($cards) }} — Nhấn thẻ để lật • ← → chuyển thẻ</p>
        <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
            <div id="progress-bar" class="bg-green-600 h-2 rounded-full transition-all" style="width: {{ count($cards) ? (100 / count($cards)) : 0 }}%"></div>
        </div>

        @if(empty($cards))
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center text-gray-500">
                Chưa có Kanji nào cho cấp {{ $level }}.
            </div>
        @else
            <div class="card-wrap mb-8" id="card-wrap">
                <div class="card relative" id="card" style="min-height: 240px;">
                    <div class="face front">
                        <span class="jp text-6xl font-bold text-gray-900" id="f">{{ $cards[0]['front'] ?? '' }}</span>
                    </div>
                    <div class="face back">
                        <span class="jp text-5xl font-bold text-gray-900 mb-3" id="bf">{{ $cards[0]['front'] ?? '' }}</span>
                        <span class="text-gray-700 text-center text-lg" id="bb">{{ $cards[0]['back'] ?? '' }}</span>
                    </div>
                </div>
            </div>
            <div class="flex gap-4">
                <button id="prev" class="flex-1 py-3 border border-gray-300 rounded-lg bg-white hover:bg-gray-50 disabled:opacity-50 font-medium" {{ count($cards) <= 1 ? 'disabled' : '' }}>← Trước</button>
                <button id="next" class="flex-1 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">Sau →</button>
            </div>
        @endif
    </div>

    @if(!empty($cards))
    <script>
        (function() {
            const cards = @json($cards);
            const N = cards.length;
            let i = 0;
            const cardEl = document.getElementById('card');
            const f = document.getElementById('f'), bf = document.getElementById('bf'), bb = document.getElementById('bb');
            const prev = document.getElementById('prev'), next = document.getElementById('next');
            const idxEl = document.getElementById('idx');
            const progressBar = document.getElementById('progress-bar');

            function go(n) {
                i = Math.max(0, Math.min(n, N - 1));
                f.textContent = bf.textContent = cards[i].front;
                bb.textContent = cards[i].back;
                cardEl.classList.remove('flip');
                idxEl.textContent = i + 1;
                progressBar.style.width = ((i + 1) / N * 100) + '%';
                prev.disabled = i === 0;
                next.textContent = i === N - 1 ? 'Xong' : 'Sau →';
            }
            cardEl.onclick = () => cardEl.classList.toggle('flip');
            prev.onclick = () => go(i - 1);
            next.onclick = () => i < N - 1 ? go(i + 1) : (location.href = '{{ route("kanji.index") }}');
            document.onkeydown = function(e) {
                if (e.key === ' ') { e.preventDefault(); cardEl.classList.toggle('flip'); return; }
                if (e.key === 'ArrowLeft') go(i - 1);
                if (e.key === 'ArrowRight') i < N - 1 ? go(i + 1) : (location.href = '{{ route("kanji.index") }}');
            };
        })();
    </script>
    @endif
    @include('layouts.footer')
</body>
</html>
