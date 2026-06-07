@php
    $searchQuery = $searchQuery ?? trim((string) request('q', ''));
    $searchType = $searchType ?? request('type', 'all');
    $suggestEnabled = $suggestEnabled ?? true;
    $variant = $variant ?? 'default';
    $isHeader = $variant === 'header';
    $inputClass = $isHeader
        ? 'global-search-input w-full rounded-full border border-gray-300 bg-white py-2.5 pl-5 pr-12 text-sm text-gray-900 shadow-sm placeholder:text-gray-500 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200'
        : 'global-search-input w-full rounded-lg border border-gray-300 bg-gray-50 py-2 pl-4 pr-11 text-sm text-gray-900 placeholder:text-gray-500 focus:border-red-500 focus:bg-white focus:outline-none focus:ring-2 focus:ring-red-200 lg:py-2.5 lg:text-base';
@endphp
<div class="global-search-wrap relative w-full" data-search-api="{{ route('search.api') }}" data-search-page="{{ route('search.index') }}">
    <form method="get" action="{{ route('search.index') }}" class="global-search-form w-full" role="search">
        @if($searchType !== 'all')
            <input type="hidden" name="type" value="{{ $searchType }}">
        @endif
        <div class="relative flex items-center">
            <input
                type="search"
                name="q"
                value="{{ $searchQuery }}"
                placeholder="Tìm từ vựng, Kanji, bài Minna, mẫu câu, ngữ pháp..."
                autocomplete="off"
                class="{{ $inputClass }}"
            />
            <button
                type="submit"
                class="absolute right-1.5 top-1/2 flex h-8 w-8 -translate-y-1/2 items-center justify-center rounded-full text-gray-500 hover:bg-gray-100 hover:text-red-600"
                aria-label="Tìm kiếm"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </button>
        </div>
    </form>

    @if($suggestEnabled)
        <div class="global-search-suggest absolute left-0 right-0 top-full z-20 mt-1 hidden max-h-[min(70vh,420px)] overflow-y-auto rounded-xl border border-gray-200 bg-white py-2 shadow-xl"
             role="listbox"
             aria-label="Gợi ý tìm kiếm"></div>
    @endif
</div>

@if($suggestEnabled)
<script>
(function () {
    const wraps = document.querySelectorAll('.global-search-wrap[data-search-api]');
    if (!wraps.length) return;

    const labels = {
        vocabulary: 'Từ vựng',
        kanji: 'Kanji',
        lessons: 'Bài Minna',
        sentence_patterns: 'Mẫu câu',
        grammar: 'Ngữ pháp',
        favorites: 'Yêu thích',
        related: 'Gợi ý',
    };

    wraps.forEach(function (wrap) {
        const input = wrap.querySelector('.global-search-input');
        const panel = wrap.querySelector('.global-search-suggest');
        const apiUrl = wrap.getAttribute('data-search-api');
        const pageUrl = wrap.getAttribute('data-search-page');
        if (!input || !panel || !apiUrl) return;

        let timer = null;
        let controller = null;

        function hidePanel() {
            panel.classList.add('hidden');
            panel.innerHTML = '';
        }

        function renderSuggest(data) {
            const groups = [
                ['vocabulary', data.vocabulary, function (r) { return (r.term || '') + (r.meaning ? ' — ' + r.meaning : ''); }],
                ['kanji', data.kanji, function (r) { return (r.character || '') + ' ' + (r.meaning || ''); }],
                ['lessons', data.lessons, function (r) { return 'Bài ' + r.number + ': ' + (r.title || ''); }],
                ['sentence_patterns', data.sentence_patterns, function (r) { return (r.pattern || '') + (r.meaning ? ' — ' + r.meaning : ''); }],
                ['grammar', data.grammar, function (r) { return r.title || r.pattern || ''; }],
                ['favorites', data.favorites, function (r) { return (r.front || '') + ' — ' + (r.back || ''); }],
                ['related', data.related, function (r) {
                    const it = r.item || {};
                    return it.term || it.character || it.pattern || r.reason || '';
                }],
            ];

            let html = '';
            groups.forEach(function (g) {
                const key = g[0];
                const items = (g[1] || []).slice(0, 3);
                if (!items.length) return;
                html += '<div class="px-3 py-1"><p class="text-xs font-semibold uppercase tracking-wide text-gray-400">' + labels[key] + '</p></div>';
                items.forEach(function (row) {
                    const text = g[2](row);
                    html += '<a href="' + pageUrl + '?q=' + encodeURIComponent(data.query || input.value.trim()) + '&type=' + key + '" class="block px-4 py-2 text-sm text-gray-800 hover:bg-red-50 hover:text-red-700">' + text.replace(/</g, '&lt;') + '</a>';
                });
            });

            if (!html) {
                html = '<p class="px-4 py-3 text-sm text-gray-500">Không có gợi ý. Nhấn Enter để tìm đầy đủ.</p>';
            } else {
                html += '<a href="' + pageUrl + '?q=' + encodeURIComponent(data.query || input.value.trim()) + '" class="block border-t border-gray-100 px-4 py-2.5 text-center text-sm font-semibold text-red-600 hover:bg-red-50">Xem tất cả kết quả</a>';
            }

            panel.innerHTML = html;
            panel.classList.remove('hidden');
        }

        input.addEventListener('input', function () {
            const q = input.value.trim();
            clearTimeout(timer);
            if (controller) controller.abort();
            if (q.length < 2) {
                hidePanel();
                return;
            }
            timer = setTimeout(function () {
                controller = new AbortController();
                fetch(apiUrl + '?q=' + encodeURIComponent(q) + '&limit=5', {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    signal: controller.signal,
                })
                    .then(function (r) { return r.ok ? r.json() : null; })
                    .then(function (data) { if (data) renderSuggest(data); })
                    .catch(function () {});
            }, 280);
        });

        input.addEventListener('focus', function () {
            if (input.value.trim().length >= 2 && panel.innerHTML) {
                panel.classList.remove('hidden');
            }
        });

        document.addEventListener('click', function (e) {
            if (!wrap.contains(e.target)) hidePanel();
        });
    });
})();
</script>
@endif
