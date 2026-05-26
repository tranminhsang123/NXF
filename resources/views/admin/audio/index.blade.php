@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Quản lý Audio/TTS</h1>
    <p class="text-gray-600 mt-2">Quản lý cache phát âm và tạo audio cho từ vựng.</p>
</div>

@if(session('success'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Trạng thái nhà cung cấp</h2>
        <div class="space-y-2 text-sm">
            <p>Đang dùng: <span class="font-bold">{{ $providerHealth['active'] }}</span></p>
            <p>Google: <span class="{{ $providerHealth['google'] ? 'text-green-700' : 'text-gray-500' }}">{{ $providerHealth['google'] ? 'đã cấu hình' : 'thiếu key' }}</span></p>
            <p>Azure: <span class="{{ $providerHealth['azure'] ? 'text-green-700' : 'text-gray-500' }}">{{ $providerHealth['azure'] ? 'đã cấu hình' : 'thiếu key/region' }}</span></p>
            <p>Forvo: <span class="{{ $providerHealth['forvo'] ? 'text-green-700' : 'text-gray-500' }}">{{ $providerHealth['forvo'] ? 'đã cấu hình' : 'thiếu key' }}</span></p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Tạo audio cho một mục</h2>
        <form method="POST" action="{{ route('admin.audio.generate') }}" class="space-y-3">
            @csrf
            <input name="text" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Từ hoặc câu tiếng Nhật">
            <input name="language" value="{{ config('pronunciation.default_language', 'ja-JP') }}" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="force" value="1"> Tạo lại, bỏ qua cache</label>
            @adminCan('audio.manage')
                <button class="rounded-lg bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700">Tạo audio</button>
            @endadminCan
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Tạo hàng loạt theo bài Minna</h2>
        <form method="POST" action="{{ route('admin.audio.bulk-generate') }}" class="space-y-3">
            @csrf
            <input type="number" min="1" name="lesson_number" class="w-full rounded-lg border border-gray-300 px-3 py-2" placeholder="Số bài">
            <input type="number" min="1" max="200" name="limit" value="80" class="w-full rounded-lg border border-gray-300 px-3 py-2">
            <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="force" value="1"> Tạo lại, bỏ qua cache</label>
            @adminCan('audio.manage')
                <button class="rounded-lg bg-blue-600 px-4 py-2 font-semibold text-white hover:bg-blue-700">Tạo hàng loạt</button>
            @endadminCan
        </form>
    </div>
</div>

<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input name="q" value="{{ request('q') }}" class="rounded-lg border border-gray-300 px-3 py-2" placeholder="Tìm theo nội dung">
        <select name="source" class="rounded-lg border border-gray-300 px-3 py-2">
            <option value="">Tất cả nguồn</option>
            @foreach(['manual', 'google', 'azure', 'forvo', 'browser'] as $source)
                <option value="{{ $source }}" @selected(request('source') === $source)>{{ $source }}</option>
            @endforeach
        </select>
        <button class="rounded-lg bg-gray-700 px-4 py-2 text-white hover:bg-gray-800">Lọc</button>
        <a href="{{ route('admin.audio.index') }}" class="rounded-lg bg-gray-200 px-4 py-2 text-center text-gray-700 hover:bg-gray-300">Đặt lại</a>
    </form>
</div>

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nội dung</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nguồn</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Lượt dùng</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Audio</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($audios as $audio)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $audio->text }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-700">{{ $audio->source }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $audio->usage_count }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if($audio->audio_url)
                                <audio controls src="{{ $audio->audio_url }}" class="h-8"></audio>
                            @else
                                <span class="text-gray-400">Dùng phát âm trình duyệt</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @adminCan('audio.manage')
                                <form method="POST" action="{{ route('admin.audio.destroy', $audio) }}" onsubmit="return confirm('Xóa cache audio này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 hover:bg-red-200">Xóa</button>
                                </form>
                            @endadminCan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-6 py-12 text-center text-gray-500">Chưa có cache audio.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($audios->hasPages())
        <div class="border-t border-gray-200 px-6 py-4">{{ $audios->links() }}</div>
    @endif
</div>
@endsection
