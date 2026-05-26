<div id="study-dictionary-popup" class="hidden fixed z-[80] w-80 max-w-[calc(100vw-2rem)] rounded-xl border border-gray-200 bg-white shadow-2xl">
    <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
        <div>
            <p class="text-xs font-bold uppercase tracking-wide text-red-600">Từ điển nhanh</p>
            <p id="study-dictionary-query" class="japanese-text text-lg font-bold text-gray-900"></p>
        </div>
        <button type="button" id="study-dictionary-close" class="rounded-full p-1 text-gray-400 hover:bg-gray-100 hover:text-gray-700">
            <span class="sr-only">Đóng</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
    <div id="study-dictionary-body" class="max-h-80 overflow-y-auto px-4 py-3 text-sm text-gray-700"></div>
</div>

<script>
    window.studyAssistConfig = {
        dictionaryUrl: @json(route('dictionary.lookup')),
        pronunciationUrl: @json(route('pronunciation.resolve')),
        favoriteUrl: @json(auth()->check() ? route('favorites.store') : route('login')),
        csrf: @json(csrf_token()),
        authenticated: @json(auth()->check()),
    };

    (function () {
        const config = window.studyAssistConfig || {};
        const popup = document.getElementById('study-dictionary-popup');
        const queryNode = document.getElementById('study-dictionary-query');
        const bodyNode = document.getElementById('study-dictionary-body');
        const closeButton = document.getElementById('study-dictionary-close');
        const japanesePattern = /[\u3040-\u30ff\u3400-\u9fff]/u;

        function escapeHtml(value) {
            return String(value || '').replace(/[&<>"']/g, function (char) {
                return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
            });
        }

        function positionPopup(x, y) {
            if (!popup) return;
            const margin = 16;
            const width = Math.min(320, window.innerWidth - margin * 2);
            const left = Math.min(Math.max(margin, x), window.innerWidth - width - margin);
            const top = Math.min(Math.max(margin, y + 12), window.innerHeight - 360);
            popup.style.left = left + 'px';
            popup.style.top = top + 'px';
        }

        function hidePopup() {
            popup?.classList.add('hidden');
        }

        function renderDictionary(data) {
            const entries = data.entries || [];
            const kanji = data.kanji || [];
            const parts = [];

            if (entries.length === 0 && kanji.length === 0) {
                parts.push('<p class="text-gray-500">Chưa tìm thấy trong dữ liệu hiện có.</p>');
            }

            entries.forEach(function (entry) {
                parts.push(`
                    <div class="mb-3 rounded-lg border border-gray-100 bg-gray-50 p-3">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="japanese-text text-base font-bold text-gray-900">${escapeHtml(entry.term || entry.kanji)}</p>
                                ${entry.kanji && entry.kanji !== '-' ? `<p class="japanese-text text-sm text-gray-600">${escapeHtml(entry.kanji)}</p>` : ''}
                            </div>
                            <div class="flex gap-1">
                                <button type="button" class="js-pronounce rounded-full border border-gray-300 px-2 py-1 text-xs font-bold text-gray-700 hover:border-red-400 hover:text-red-600" data-pronounce-text="${escapeHtml(entry.term || entry.kanji)}">Nghe</button>
                                <button type="button" class="js-favorite rounded-full border border-emerald-300 px-2 py-1 text-xs font-bold text-emerald-700 hover:bg-emerald-50" data-favorite-front="${escapeHtml(entry.term || entry.kanji)}" data-favorite-back="${escapeHtml(entry.meaning)}" data-favorite-lesson="${escapeHtml(entry.lesson_number || '')}">Lưu</button>
                            </div>
                        </div>
                        <p class="mt-2 font-semibold text-gray-900">${escapeHtml(entry.meaning)}</p>
                        ${entry.note ? `<p class="mt-1 text-xs text-gray-500">${escapeHtml(entry.note)}</p>` : ''}
                        ${entry.lesson_number ? `<p class="mt-2 text-xs text-red-600">Minna bài ${escapeHtml(entry.lesson_number)} - ${escapeHtml(entry.lesson_title || '')}</p>` : ''}
                    </div>
                `);
            });

            kanji.forEach(function (item) {
                parts.push(`
                    <div class="mb-3 rounded-lg border border-gray-100 bg-white p-3">
                        <p class="japanese-text text-xl font-bold text-gray-900">${escapeHtml(item.character)}</p>
                        <p class="mt-1 font-semibold text-gray-900">${escapeHtml(item.meaning)}</p>
                        <p class="mt-1 text-xs text-gray-500">Âm On: ${escapeHtml(item.on_reading || '-')} · Âm Kun: ${escapeHtml(item.kun_reading || '-')} · ${escapeHtml(item.level || '')}</p>
                    </div>
                `);
            });

            if (bodyNode) {
                bodyNode.innerHTML = parts.join('');
            }
        }

        async function lookup(term, event) {
            term = String(term || '').trim();
            if (!term || !japanesePattern.test(term) || !config.dictionaryUrl) {
                return;
            }

            if (queryNode) queryNode.textContent = term;
            if (bodyNode) bodyNode.innerHTML = '<p class="text-gray-500">Đang tra...</p>';
            positionPopup(event.clientX || 24, event.clientY || 24);
            popup?.classList.remove('hidden');

            try {
                const url = config.dictionaryUrl + '?q=' + encodeURIComponent(term);
                const response = await fetch(url, { headers: { Accept: 'application/json' } });
                if (!response.ok) throw new Error('lookup failed');
                renderDictionary(await response.json());
            } catch (error) {
                if (bodyNode) bodyNode.innerHTML = '<p class="text-red-600">Không tra được lúc này.</p>';
            }
        }

        async function pronounce(text) {
            text = String(text || '').trim();
            if (!text) return;

            try {
                if (config.pronunciationUrl) {
                    const url = config.pronunciationUrl + '?text=' + encodeURIComponent(text) + '&language=ja-JP';
                    const response = await fetch(url, { headers: { Accept: 'application/json' } });
                    if (response.ok) {
                        const data = await response.json();
                        const audioUrl = data.audio?.audio_url;
                        if (audioUrl) {
                            new Audio(audioUrl).play();
                            return;
                        }
                    }
                }
            } catch (error) {}

            if ('speechSynthesis' in window) {
                const utterance = new SpeechSynthesisUtterance(text);
                utterance.lang = 'ja-JP';
                utterance.rate = 0.9;
                window.speechSynthesis.cancel();
                window.speechSynthesis.speak(utterance);
            }
        }

        window.studyAssistPronounce = pronounce;

        async function saveFavorite(button) {
            if (!config.authenticated) {
                window.location.href = config.favoriteUrl;
                return;
            }

            const front = button.getAttribute('data-favorite-front') || '';
            const back = button.getAttribute('data-favorite-back') || '';
            if (!front || !back) return;

            const originalText = button.textContent;
            button.disabled = true;
            button.textContent = 'Đang lưu';

            try {
                const response = await fetch(config.favoriteUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': config.csrf,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        front,
                        back,
                        item_type: 'vocabulary',
                        source_type: button.getAttribute('data-favorite-source') || 'minna',
                        lesson_number: button.getAttribute('data-favorite-lesson') || null,
                    }),
                });
                if (!response.ok) throw new Error('favorite failed');
                button.textContent = 'Đã lưu';
                button.classList.add('bg-emerald-600', 'text-white');
            } catch (error) {
                button.textContent = 'Lỗi';
                window.setTimeout(function () {
                    button.textContent = originalText || 'Lưu';
                    button.disabled = false;
                }, 1400);
                return;
            }

            window.setTimeout(function () {
                button.disabled = false;
            }, 800);
        }

        document.addEventListener('click', function (event) {
            const pronounceButton = event.target.closest('.js-pronounce');
            if (pronounceButton) {
                event.preventDefault();
                event.stopPropagation();
                pronounce(pronounceButton.getAttribute('data-pronounce-text') || pronounceButton.textContent);
                return;
            }

            const favoriteButton = event.target.closest('.js-favorite');
            if (favoriteButton) {
                event.preventDefault();
                event.stopPropagation();
                saveFavorite(favoriteButton);
                return;
            }

            const lookupTarget = event.target.closest('[data-dictionary-term], .japanese-text');
            if (lookupTarget) {
                const term = lookupTarget.getAttribute('data-dictionary-term') || lookupTarget.textContent;
                lookup(term, event);
            }
        });

        document.addEventListener('mouseup', function (event) {
            const selection = window.getSelection()?.toString().trim();
            if (selection && selection.length <= 80) {
                lookup(selection, event);
            }
        });

        closeButton?.addEventListener('click', hidePopup);
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                hidePopup();
            }
        });
    })();
</script>
