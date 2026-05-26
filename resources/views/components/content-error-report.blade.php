@php
    $contentReportEnabled = request()->routeIs([
        'minna.*',
        'flashcard.*',
        'course.*',
        'vocabulary.*',
        'kanji.*',
        'alphabet.index',
    ]);

    $contentReportType = 'page';
    $contentReportId = null;
    $contentReportTitle = trim($__env->yieldContent('title')) ?: null;

    if (isset($section) && is_object($section) && method_exists($section, 'getKey')) {
        $contentReportType = str_contains($section::class, 'Minna') ? 'minna_section' : 'section';
        $contentReportId = $section->getKey();
        $contentReportTitle = $section->title ?? $contentReportTitle;
    } elseif (isset($lesson) && is_object($lesson) && method_exists($lesson, 'getKey')) {
        $contentReportType = str_contains($lesson::class, 'Minna') ? 'minna_lesson' : 'lesson';
        $contentReportId = $lesson->getKey();
        $contentReportTitle = $lesson->title ?? $contentReportTitle;
    } elseif (request()->routeIs('flashcard.*')) {
        $contentReportType = 'flashcard';
        $contentReportTitle = $deckTitle ?? ($lesson->title ?? 'Flashcard');
    } elseif (request()->routeIs('course.*')) {
        $contentReportType = 'course';
        $contentReportTitle = $level ?? 'Khóa học';
    } elseif (request()->routeIs('vocabulary.*')) {
        $contentReportType = 'vocabulary';
        $contentReportTitle = 'Từ vựng';
    } elseif (request()->routeIs('kanji.*')) {
        $contentReportType = 'kanji';
        $contentReportTitle = 'Kanji';
    } elseif (request()->routeIs('alphabet.index')) {
        $contentReportType = 'alphabet';
        $contentReportTitle = 'Bảng chữ cái';
    }

    $contentReportContext = [
        'content_type' => $contentReportType,
        'content_id' => $contentReportId,
        'content_title' => $contentReportTitle,
        'route' => request()->route()?->getName(),
    ];
@endphp

@if($contentReportEnabled)
<button type="button"
        id="content-report-open"
        class="fixed left-4 bottom-4 z-40 inline-flex h-11 items-center gap-2 rounded-full bg-gray-900 px-4 text-sm font-semibold text-white shadow-lg hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M12 9v4"></path>
        <path d="M12 17h.01"></path>
        <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z"></path>
    </svg>
    Báo lỗi
</button>

<div id="content-report-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" data-content-report-close="1"></div>
    <div class="relative mx-auto mt-24 w-[calc(100%-2rem)] max-w-lg rounded-lg bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-200 px-5 py-4">
            <h2 class="text-lg font-bold text-gray-900">Báo lỗi nội dung</h2>
            <button type="button" class="rounded p-1 text-gray-500 hover:bg-gray-100 hover:text-gray-800" data-content-report-close="1" aria-label="Đóng">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
        </div>
        <form id="content-report-form" class="space-y-4 px-5 py-5">
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Loại lỗi</label>
                <select name="category" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                    @foreach(\App\Models\ContentErrorReport::categoryLabels() as $category => $label)
                        <option value="{{ $category }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Đoạn đang chọn</label>
                <div id="content-report-selected-preview" class="min-h-10 rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm text-gray-600">Không chọn đoạn nào.</div>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Mô tả lỗi</label>
                <textarea name="description" rows="4" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Ví dụ: audio đọc sai, nghĩa tiếng Việt chưa đúng, đáp án quiz bị nhầm..." required></textarea>
            </div>
            <p id="content-report-status" class="hidden rounded-lg px-3 py-2 text-sm"></p>
            <div class="flex justify-end gap-2">
                <button type="button" class="rounded-lg bg-gray-200 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-300" data-content-report-close="1">Huỷ</button>
                <button id="content-report-submit" class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">Gửi báo lỗi</button>
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        const openButton = document.getElementById('content-report-open');
        const modal = document.getElementById('content-report-modal');
        const form = document.getElementById('content-report-form');
        const preview = document.getElementById('content-report-selected-preview');
        const statusBox = document.getElementById('content-report-status');
        const submitButton = document.getElementById('content-report-submit');
        const reportUrl = @json(route('content-reports.store'));
        const csrfToken = @json(csrf_token());
        const context = @json($contentReportContext);

        if (!openButton || !modal || !form) return;

        let selectedText = '';

        function closeModal() {
            modal.classList.add('hidden');
            statusBox.classList.add('hidden');
            statusBox.textContent = '';
            form.reset();
        }

        openButton.addEventListener('click', function () {
            selectedText = String(window.getSelection?.().toString() || '').trim().slice(0, 1000);
            preview.textContent = selectedText || 'Không chọn đoạn nào.';
            modal.classList.remove('hidden');
            form.querySelector('textarea[name="description"]')?.focus();
        });

        document.querySelectorAll('[data-content-report-close="1"]').forEach(function (node) {
            node.addEventListener('click', closeModal);
        });

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            if (submitButton.disabled) return;

            const formData = new FormData(form);
            const description = String(formData.get('description') || '').trim();
            if (!description) return;

            submitButton.disabled = true;
            statusBox.className = 'rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700';
            statusBox.textContent = 'Đang gửi...';

            try {
                const response = await fetch(reportUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        category: formData.get('category'),
                        description: description,
                        selected_text: selectedText,
                        content_type: context.content_type,
                        content_id: context.content_id,
                        content_title: context.content_title || document.title,
                        page_url: window.location.href,
                        browser_context: {
                            route: context.route,
                            title: document.title,
                            viewport: window.innerWidth + 'x' + window.innerHeight,
                        }
                    })
                });

                if (!response.ok) throw new Error('report failed');

                statusBox.className = 'rounded-lg bg-green-50 px-3 py-2 text-sm text-green-800';
                statusBox.textContent = 'Đã gửi báo lỗi cho admin.';
                setTimeout(closeModal, 900);
            } catch (error) {
                statusBox.className = 'rounded-lg bg-red-50 px-3 py-2 text-sm text-red-800';
                statusBox.textContent = 'Không gửi được báo lỗi. Bạn thử lại giúp mình nhé.';
            } finally {
                submitButton.disabled = false;
            }
        });
    })();
</script>
@endif
