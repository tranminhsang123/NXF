@extends('adminlayout.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Inbox hỗ trợ</h1>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <section class="lg:col-span-2 bg-white rounded-lg border border-gray-200 p-5">
        <h2 class="font-semibold text-gray-900 mb-3">Hội thoại gần đây</h2>
        <div class="divide-y divide-gray-100">
            @forelse($conversations as $c)
                @php $last = $c->messages->first(); @endphp
                <a href="{{ route('admin.inbox.show', ['user' => $c->user_id]) }}" class="block py-3 hover:bg-gray-50 rounded px-2">
                    <div class="flex items-center justify-between gap-2">
                        <div class="font-medium text-gray-900">{{ $c->user?->name ?? 'User' }}</div>
                        @if(($c->unread_count ?? 0) > 0)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-100 text-red-700">{{ $c->unread_count }}</span>
                        @endif
                    </div>
                    <div class="text-sm text-gray-500 truncate">{{ $last?->content ?? 'Chưa có tin nhắn' }}</div>
                </a>
            @empty
                <div class="text-sm text-gray-500 py-6">Chưa có hội thoại nào.</div>
            @endforelse
        </div>
    </section>

    <aside class="bg-white rounded-lg border border-gray-200 p-5">
        <h2 class="font-semibold text-gray-900 mb-3">Nhắn cho user</h2>
        <div class="space-y-2 max-h-[60vh] overflow-y-auto">
            @foreach($users as $user)
                <a href="{{ route('admin.inbox.show', ['user' => $user->id]) }}" class="block rounded-lg border border-gray-200 px-3 py-2 hover:border-red-300 hover:bg-red-50">
                    <div class="text-sm font-medium text-gray-900 truncate">{{ $user->name }}</div>
                    <div class="text-xs text-gray-500 truncate">{{ $user->email }}</div>
                </a>
            @endforeach
        </div>
    </aside>
</div>
@endsection

