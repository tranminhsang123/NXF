@extends('adminlayout.app')

@section('content')
    <div class="mb-6">
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Nhóm chat: {{ $group->name }}</h1>
                <p class="text-gray-500 text-sm mt-1">
                    ID: {{ $group->id }} · Thành viên: {{ $group->members()->count() }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.chat.groups.edit', ['group' => $group->id]) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-900 border border-gray-200 px-4 py-2 rounded-lg text-sm font-semibold">
                    Sửa tên
                </a>

                <form method="POST" action="{{ route('admin.chat.groups.destroy', ['group' => $group->id]) }}"
                      onsubmit="return confirm('Xóa nhóm chat này? Các tin nhắn liên quan cũng sẽ bị xóa.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                        Xóa nhóm
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="text-lg font-semibold text-gray-900">50 tin nhắn gần nhất</h2>
            <a href="{{ route('admin.chat.groups.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                ← Quay lại
            </a>
        </div>

        @if($messages->isEmpty())
            <div class="text-gray-500 text-sm">Chưa có tin nhắn.</div>
        @else
            <div class="space-y-3">
                @foreach($messages as $m)
                    @php $isMe = $m->sender_id === auth()->id(); @endphp
                    <div class="flex {{ $isMe ? 'justify-end' : 'justify-start' }}">
                        <div class="{{ $isMe ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-900' }} rounded-2xl px-4 py-3 max-w-[92%] md:max-w-[70%]">
                            <div class="text-xs opacity-80 mb-1">
                                {{ $isMe ? 'Bạn (admin)' : ($m->sender?->name ?? 'User') }}
                            </div>
                            <div class="whitespace-pre-wrap break-words text-sm">{{ $m->content }}</div>
                            <div class="text-[10px] opacity-70 mt-2 text-right">
                                {{ $m->created_at?->format('H:i') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <div class="mb-4">
            <h2 class="text-lg font-semibold text-gray-900">Yêu cầu xin tham gia (chờ duyệt)</h2>
            <p class="text-sm text-gray-500 mt-1">Admin duyệt để user trở thành thành viên nhóm.</p>
        </div>

        @if(($joinRequests ?? collect())->isEmpty())
            <div class="text-sm text-gray-500">Không có yêu cầu nào.</div>
        @else
            <div class="space-y-3">
                @foreach($joinRequests as $jr)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <div class="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $jr->user->name }}</div>
                                <div class="text-xs text-gray-500">{{ $jr->user->email }}</div>
                                <div class="text-xs text-gray-400 mt-1">
                                    Thời gian: {{ $jr->created_at?->format('d/m/Y H:i') }}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <form method="POST"
                                    action="{{ route('admin.chat.groups.join-requests.approve', ['joinRequest' => $jr->id]) }}"
                                      onsubmit="return confirm('Duyệt yêu cầu?')">
                                    @csrf
                                    <button type="submit"
                                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-xs font-semibold">
                                        Duyệt
                                    </button>
                                </form>
                                <form method="POST"
                                      action="{{ route('admin.chat.groups.join-requests.decline', ['joinRequest' => $jr->id]) }}"
                                      onsubmit="return confirm('Từ chối yêu cầu?')">
                                    @csrf
                                    <button type="submit"
                                            class="bg-gray-700 hover:bg-gray-800 text-white px-4 py-2 rounded-lg text-xs font-semibold">
                                        Từ chối
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

