@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Quản lý Kanji</h1>
        @adminCan('kanjis.edit')
        <a href="{{ route('admin.kanjis.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-center">
            + Thêm Kanji mới
        </a>
        @endadminCan
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Cấp độ</label>
            <select name="level" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                <option value="N5" {{ request('level') == 'N5' ? 'selected' : '' }}>N5</option>
                <option value="N4" {{ request('level') == 'N4' ? 'selected' : '' }}>N4</option>
                <option value="N3" {{ request('level') == 'N3' ? 'selected' : '' }}>N3</option>
                <option value="N2" {{ request('level') == 'N2' ? 'selected' : '' }}>N2</option>
                <option value="N1" {{ request('level') == 'N1' ? 'selected' : '' }}>N1</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Ký tự, nghĩa, cách đọc..." 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 mr-2">
                Lọc
            </button>
            <a href="{{ route('admin.kanjis.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Kanji Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full min-w-[920px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ký tự</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nghĩa</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Âm On</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Âm Kun</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cấp độ</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số nét</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($kanjis as $kanji)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-3xl font-bold text-gray-900">{{ $kanji->character }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ $kanji->meaning }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-600">{{ $kanji->on_reading ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-600">{{ $kanji->kun_reading ?? '-' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $kanji->level == 'N5' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $kanji->level == 'N4' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $kanji->level == 'N3' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $kanji->level == 'N2' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $kanji->level == 'N1' ? 'bg-purple-100 text-purple-800' : '' }}">
                        {{ $kanji->level }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-600">{{ $kanji->stroke_count }} nét</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        @adminCan('kanjis.view')
                        <a href="{{ route('admin.kanjis.show', $kanji) }}"
                           class="text-blue-600 hover:text-blue-900">Xem</a>
                        @endadminCan
                        @adminCan('kanjis.edit')
                        <a href="{{ route('admin.kanjis.edit', $kanji) }}"
                           class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                        @endadminCan
                        @adminCan('kanjis.delete')
                        <form action="{{ route('admin.kanjis.destroy', $kanji) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Bạn có chắc muốn xóa Kanji này?')">Xóa</button>
                        </form>
                        @endadminCan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                    <div class="text-lg">Chưa có Kanji nào</div>
                    <div class="text-sm mt-2">Hãy thêm Kanji đầu tiên!</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<!-- Pagination -->
@if($kanjis->hasPages())
<div class="mt-6">
    {{ $kanjis->appends(request()->query())->links() }}
</div>
@endif
@endsection

