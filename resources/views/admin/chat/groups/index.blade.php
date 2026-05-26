@extends('adminlayout.app')

@section('content')
    <div class="mb-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Nhóm chat (admin)</h1>
            @adminCan('chat_groups.edit')
            <a href="{{ route('admin.chat.groups.create') }}"
               class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-semibold text-center">
                + Tạo nhóm chat
            </a>
            @endadminCan
        </div>
        @if(session('status'))
            <div class="mt-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg text-sm">
                {{ session('status') }}
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        @if($groups->isEmpty())
            <div class="text-gray-600 text-sm">Chưa có nhóm chat nào.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="py-2 pr-4">Tên nhóm</th>
                            <th class="py-2 pr-4">Thành viên</th>
                            <th class="py-2 pr-4">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groups as $group)
                            <tr class="border-t">
                                <td class="py-3 pr-4 font-semibold text-gray-900">
                                    {{ $group->name }}
                                </td>
                                <td class="py-3 pr-4 text-gray-700">
                                    {{ $group->members_count }}
                                </td>
                                <td class="py-3 pr-4 text-gray-500">
                                    {{ $group->created_at?->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @adminCan('chat_groups.view')
                                        <a href="{{ route('admin.chat.groups.show', ['group' => $group->id]) }}"
                                           class="bg-gray-100 hover:bg-gray-200 text-gray-900 border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-semibold">
                                            Xem
                                        </a>
                                        @endadminCan
                                        @adminCan('chat_groups.edit')
                                        <a href="{{ route('admin.chat.groups.edit', ['group' => $group->id]) }}"
                                           class="bg-gray-100 hover:bg-gray-200 text-gray-900 border border-gray-200 px-3 py-1.5 rounded-lg text-xs font-semibold">
                                            Sửa
                                        </a>
                                        @endadminCan
                                        @adminCan('chat_groups.delete')
                                        <form method="POST"
                                              action="{{ route('admin.chat.groups.destroy', ['group' => $group->id]) }}"
                                              onsubmit="return confirm('Xóa nhóm chat?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-semibold">
                                                Xóa
                                            </button>
                                        </form>
                                        @endadminCan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

