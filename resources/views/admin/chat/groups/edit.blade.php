@extends('adminlayout.app')

@section('content')
    <div class="mb-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Sửa nhóm chat</h1>
            <a href="{{ route('admin.chat.groups.show', ['group' => $group->id]) }}"
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-semibold text-center">
                ← Quay lại
            </a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <form method="POST" action="{{ route('admin.chat.groups.update', ['group' => $group->id]) }}">
            @csrf
            @method('PUT')

            <div class="mb-5">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Tên nhóm *</label>
                <input id="name" name="name" type="text"
                       value="{{ old('name', $group->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500
                              @error('name') border-red-500 @enderror"
                       required>
                @error('name')
                    <div class="text-red-600 text-sm mt-2">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <div class="flex items-center justify-between">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Chọn user tham gia nhóm</label>
                    <div class="text-xs text-gray-500">Nếu không tick, user sẽ bị xóa khỏi nhóm.</div>
                </div>

                <div class="border border-gray-200 rounded-lg p-3 max-h-[420px] overflow-y-auto">
                    @foreach($users as $u)
                        <label class="flex items-center gap-3 py-2 px-2 rounded-md hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox"
                                   name="user_ids[]"
                                   value="{{ $u->id }}"
                                   class="h-4 w-4 accent-red-600"
                                   {{ in_array($u->id, $memberIds) ? 'checked' : '' }}>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $u->name }}</div>
                                <div class="text-xs text-gray-500">{{ $u->email }}</div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('admin.chat.groups.show', ['group' => $group->id]) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-900 border border-gray-200 px-5 py-3 rounded-lg text-sm font-semibold">
                    Hủy
                </a>
                <button type="submit"
                        class="bg-red-600 hover:bg-red-700 text-white px-5 py-3 rounded-lg text-sm font-semibold">
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
@endsection

