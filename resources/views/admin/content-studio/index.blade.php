@extends('adminlayout.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Xưởng nội dung</h1>
        <p class="text-gray-600 mt-2">Soạn bài nhanh, nhập dữ liệu, tạo quiz/flashcard và kiểm tra chất lượng trước khi xuất bản.</p>
    </div>
    <a href="{{ route('admin.content-ops.index', ['type' => 'minna_lesson']) }}" class="inline-flex items-center justify-center rounded-lg bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">
        Mở luồng QA / Xuất bản
    </a>
</div>

@if(session('success'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif
@if($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
@endif
@if(session('import_result'))
    @php($result = session('import_result'))
    <div class="mb-6 rounded-lg border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800">
        Import xong: {{ $result['item_count'] ?? 0 }} mục, {{ $result['section_count'] ?? 0 }} phần, bỏ qua {{ $result['skipped_rows'] ?? 0 }} dòng.
    </div>
@endif

<div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5 mb-6">
    <a href="{{ route('admin.content-studio.index') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-red-200">
        <p class="text-sm text-gray-500">Tổng bài Minna</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $overview['total_lessons'] }}</p>
    </a>
    <a href="{{ route('admin.content-studio.index', ['status' => 'draft']) }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-amber-200">
        <p class="text-sm text-gray-500">Bài nháp</p>
        <p class="mt-2 text-3xl font-bold text-amber-600">{{ $overview['draft_lessons'] }}</p>
    </a>
    <a href="{{ route('admin.content-studio.index') }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-green-200">
        <p class="text-sm text-gray-500">Đạt QA</p>
        <p class="mt-2 text-3xl font-bold text-green-600">{{ $overview['ready_lessons'] }}</p>
    </a>
    <a href="{{ route('admin.content-studio.index', ['quality' => 'missing_audio']) }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-red-200">
        <p class="text-sm text-gray-500">Thiếu audio</p>
        <p class="mt-2 text-3xl font-bold text-red-600">{{ $overview['missing_audio_lessons'] }}</p>
    </a>
    <a href="{{ route('admin.content-studio.index', ['quality' => 'missing_quiz']) }}" class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm hover:border-red-200">
        <p class="text-sm text-gray-500">Thiếu quiz</p>
        <p class="mt-2 text-3xl font-bold text-red-600">{{ $overview['missing_quiz_lessons'] }}</p>
    </a>
</div>

<div class="grid grid-cols-1 gap-6 xl:grid-cols-2 mb-6">
    <div class="rounded-lg bg-white p-6 shadow-sm">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-900">Tạo bài học theo template</h2>
            <p class="mt-1 text-sm text-gray-500">Tạo nhanh bài Minna mới với đủ 5 phần: từ vựng, ngữ pháp, luyện đọc, hội thoại, Hán tự.</p>
        </div>
        @adminCan('content_ops.edit')
            <form method="POST" action="{{ route('admin.content-studio.template') }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Số bài</label>
                    <input type="number" name="number" value="{{ old('number') }}" min="1" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Tự tăng">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Tiêu đề</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Ví dụ: Bài 50 - Kính ngữ" required>
                </div>
                <div class="flex items-end">
                    <button class="w-full rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">Tạo bài</button>
                </div>
                <div class="md:col-span-4">
                    <label class="mb-2 block text-sm font-medium text-gray-700">Mô tả ngắn</label>
                    <textarea name="description" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Mục tiêu bài học, tình huống hoặc ghi chú cho editor">{{ old('description') }}</textarea>
                </div>
            </form>
        @else
            <p class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-600">Bạn chỉ có quyền xem Xưởng nội dung.</p>
        @endadminCan
    </div>

    <div class="rounded-lg bg-white p-6 shadow-sm">
        <div class="mb-4">
            <h2 class="text-lg font-bold text-gray-900">Import từ CSV / Excel</h2>
            <p class="mt-1 text-sm text-gray-500">Hỗ trợ cột: lesson_number, title, section_key, group, jp hoặc tu_vung, nghia hoặc meaning.</p>
        </div>
        @adminCan('content_ops.edit')
            <form method="POST" action="{{ route('admin.content-studio.import') }}" enctype="multipart/form-data" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                @csrf
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700">Bài mặc định</label>
                    <input type="number" name="lesson_number" min="1" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Nếu file thiếu cột">
                </div>
                <div class="md:col-span-2">
                    <label class="mb-2 block text-sm font-medium text-gray-700">File dữ liệu</label>
                    <input type="file" name="file" accept=".csv,.tsv,.txt,.xlsx,.xls" class="w-full rounded-lg border border-gray-300 px-3 py-2" required>
                </div>
                <div class="flex items-end">
                    <button class="w-full rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Import</button>
                </div>
                <div class="md:col-span-4 rounded-lg bg-gray-50 px-4 py-3 text-xs text-gray-600">
                    section_key có thể là tu-vung, ngu-phap, luyen-doc, hoi-thoai hoặc han-tu. File Excel sẽ đọc sheet đầu tiên.
                </div>
            </form>
        @else
            <p class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-600">Bạn chỉ có quyền xem Xưởng nội dung.</p>
        @endadminCan
    </div>
</div>

<div class="rounded-lg bg-white p-6 shadow-sm mb-6">
    <form method="GET" action="{{ route('admin.content-studio.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-5">
        <div class="md:col-span-2">
            <label class="mb-2 block text-sm font-medium text-gray-700">Tìm bài</label>
            <input type="text" name="q" value="{{ request('q') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Số bài, tiêu đề, mô tả">
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Trạng thái</label>
            <select name="status" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                <option value="">Tất cả</option>
                @foreach($statuses as $status => $label)
                    <option value="{{ $status }}" @selected(request('status') === $status)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="mb-2 block text-sm font-medium text-gray-700">Chất lượng</label>
            <select name="quality" class="w-full rounded-lg border border-gray-300 px-3 py-2">
                <option value="">Tất cả</option>
                <option value="missing_audio" @selected(request('quality') === 'missing_audio')>Thiếu audio</option>
                <option value="missing_quiz" @selected(request('quality') === 'missing_quiz')>Thiếu quiz</option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button class="rounded-lg bg-gray-800 px-4 py-2 font-semibold text-white hover:bg-gray-900">Lọc</button>
            <a href="{{ route('admin.content-studio.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 font-semibold text-gray-700 hover:bg-gray-300">Đặt lại</a>
        </div>
    </form>
</div>

<div class="overflow-hidden rounded-lg bg-white shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Bài học</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Audio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Quiz</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">Flashcard</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500">QA</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($lessons as $lesson)
                    @php($diag = $diagnosticsByLesson[$lesson->id] ?? [])
                    <tr>
                        <td class="px-6 py-4 align-top">
                            <p class="font-bold text-gray-900">Bài {{ $lesson->number }} - {{ $lesson->title }}</p>
                            <p class="mt-1 text-xs text-gray-500">{{ $lesson->sections_count }} phần • {{ $lesson->publishStatusLabel() }} • cập nhật {{ $lesson->updated_at?->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="px-6 py-4 align-top text-sm">
                            @if(($diag['missing_audio_count'] ?? 0) > 0)
                                <span class="rounded bg-red-50 px-2 py-1 font-semibold text-red-700">Thiếu {{ $diag['missing_audio_count'] }}/{{ $diag['audio_required'] }}</span>
                                <p class="mt-2 max-w-xs text-xs text-gray-500">{{ implode(', ', $diag['missing_audio_samples'] ?? []) }}</p>
                            @else
                                <span class="rounded bg-green-50 px-2 py-1 font-semibold text-green-700">Đủ {{ $diag['audio_required'] ?? 0 }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-top text-sm">
                            @if($diag['missing_quiz'] ?? true)
                                <span class="rounded bg-amber-50 px-2 py-1 font-semibold text-amber-700">Cần bổ sung</span>
                            @else
                                <span class="rounded bg-green-50 px-2 py-1 font-semibold text-green-700">Đủ dữ liệu</span>
                            @endif
                            <p class="mt-2 text-xs text-gray-500">Mini {{ $diag['mini_quiz_count'] ?? 0 }} • nâng cao {{ $diag['advanced_quiz_count'] ?? 0 }} • đã lưu {{ $diag['generated_quiz_count'] ?? 0 }}</p>
                        </td>
                        <td class="px-6 py-4 align-top text-sm">
                            <span class="rounded bg-gray-100 px-2 py-1 font-semibold text-gray-700">{{ $diag['flashcard_count'] ?? 0 }} thẻ</span>
                            <p class="mt-2 text-xs text-gray-500">Đã lưu {{ $diag['generated_flashcard_count'] ?? 0 }} thẻ từ Studio</p>
                        </td>
                        <td class="px-6 py-4 align-top text-sm">
                            @if($diag['qa_passed'] ?? false)
                                <span class="rounded bg-green-50 px-2 py-1 font-semibold text-green-700">Sẵn sàng</span>
                            @else
                                <span class="rounded bg-red-50 px-2 py-1 font-semibold text-red-700">{{ $diag['qa_blocking_count'] ?? 0 }} mục chặn</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-top text-right text-sm">
                            <div class="flex flex-wrap justify-end gap-2">
                                @adminCan('content_ops.edit')
                                    <form method="POST" action="{{ route('admin.content-studio.generate-quiz', $lesson) }}">
                                        @csrf
                                        <button class="rounded bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Tạo quiz</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.content-studio.generate-flashcards', $lesson) }}">
                                        @csrf
                                        <button class="rounded bg-violet-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-violet-700">Tạo flashcard</button>
                                    </form>
                                @endadminCan
                                <a href="{{ route('admin.content-studio.preview', $lesson) }}" class="rounded bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200">Xem như người học</a>
                                <a href="{{ route('admin.content-studio.compare', $lesson) }}" class="rounded bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200">So sánh</a>
                                <a href="{{ route('admin.minna.show', $lesson) }}" class="rounded bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-700 hover:bg-gray-200">Sửa bài</a>
                                <a href="{{ route('admin.content-ops.preview', ['type' => 'minna_lesson', 'id' => $lesson->id]) }}" class="rounded bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-100">QA</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">Chưa có bài học phù hợp.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($lessons->hasPages())
        <div class="border-t border-gray-200 px-6 py-4">{{ $lessons->links() }}</div>
    @endif
</div>
@endsection
