@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Quản lý Minna no Nihongo</h1>
        @adminCan('minna.edit')
        <a href="{{ route('admin.minna.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-center">
            + Thêm bài học mới
        </a>
        @endadminCan
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Số bài hoặc tiêu đề..." 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 mr-2">
                Lọc
            </button>
            <a href="{{ route('admin.minna.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Lessons Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full min-w-[760px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số bài</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tiêu đề</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Số phần</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($lessons as $lesson)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-lg font-bold text-gray-900">Bài {{ str_pad($lesson->number, 2, '0', STR_PAD_LEFT) }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $lesson->title }}</div>
                    @if($lesson->description)
                        <div class="text-sm text-gray-500 mt-1">{{ $lesson->description }}</div>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                        {{ $lesson->sections_count }} phần
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        @adminCan('minna.view')
                        <a href="{{ route('admin.minna.show', $lesson) }}"
                           class="text-green-600 hover:text-green-900">Xem</a>
                        @endadminCan
                        @adminCan('minna.edit')
                        <a href="{{ route('admin.minna.edit', $lesson) }}"
                           class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                        @endadminCan
                        @adminCan('minna.delete')
                        <form action="{{ route('admin.minna.destroy', $lesson) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Bạn có chắc muốn xóa bài học này?')">Xóa</button>
                        </form>
                        @endadminCan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                    <div class="text-lg">Chưa có bài học nào</div>
                    <div class="text-sm mt-2">Hãy thêm bài học đầu tiên!</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<!-- Pagination -->
@if($lessons->hasPages())
<div class="mt-6">
    {{ $lessons->appends(request()->query())->links() }}
</div>
@endif
@endsection

