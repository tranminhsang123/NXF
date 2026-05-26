@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Quản lý bảng chữ cái</h1>
        @adminCan('alphabets.edit')
        <a href="{{ route('admin.alphabets.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 text-center">
            + Thêm ký tự mới
        </a>
        @endadminCan
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Loại</label>
            <select name="type" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                <option value="">Tất cả</option>
                <option value="hiragana" {{ request('type') == 'hiragana' ? 'selected' : '' }}>Hiragana</option>
                <option value="katakana" {{ request('type') == 'katakana' ? 'selected' : '' }}>Katakana</option>
                <option value="romaji" {{ request('type') == 'romaji' ? 'selected' : '' }}>Romaji</option>
            </select>
        </div>
        
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
            <input type="text" name="search" value="{{ request('search') }}" 
                   placeholder="Ký tự hoặc romaji..." 
                   class="w-full border border-gray-300 rounded-lg px-3 py-2">
        </div>
        
        <div class="flex items-end">
            <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 mr-2">
                Lọc
            </button>
            <a href="{{ route('admin.alphabets.index') }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Alphabet Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full min-w-[720px]">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ký tự</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Romaji</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loại</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phân loại</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Thao tác</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($alphabets as $alphabet)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-2xl font-bold text-gray-900">{{ $alphabet->character }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-gray-900">{{ $alphabet->romaji }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $alphabet->type == 'hiragana' ? 'bg-red-100 text-red-800' : '' }}
                        {{ $alphabet->type == 'katakana' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $alphabet->type == 'romaji' ? 'bg-blue-100 text-blue-800' : '' }}">
                        {{ ucfirst($alphabet->type) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-600">
                        @if($alphabet->category)
                            @if($alphabet->category == 'seion') Seion (清音)
                            @elseif($alphabet->category == 'dakuon') Dakuon (濁音)
                            @elseif($alphabet->category == 'yoon') Yōon (拗音)
                            @else {{ $alphabet->category }}
                            @endif
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        @adminCan('alphabets.edit')
                        <a href="{{ route('admin.alphabets.edit', $alphabet) }}"
                           class="text-indigo-600 hover:text-indigo-900">Sửa</a>
                        @endadminCan
                        @adminCan('alphabets.delete')
                        <form action="{{ route('admin.alphabets.destroy', $alphabet) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900"
                                    onclick="return confirm('Bạn có chắc muốn xóa ký tự này?')">Xóa</button>
                        </form>
                        @endadminCan
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                    <div class="text-lg">Chưa có ký tự nào</div>
                    <div class="text-sm mt-2">Hãy thêm ký tự đầu tiên!</div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    </div>
</div>

<!-- Pagination -->
@if($alphabets->hasPages())
<div class="mt-6">
    {{ $alphabets->appends(request()->query())->links() }}
</div>
@endif
@endsection
