@extends('adminlayout.app')

@section('content')
<div class="mb-6 flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Thông báo</h1>
    @if($notifications->total() > 0)
        <form method="POST" action="{{ route('admin.notifications.mark-all-read') }}" class="inline">
            @csrf
            <button type="submit" class="px-4 py-2 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg transition">
                Đánh dấu tất cả đã đọc
            </button>
        </form>
    @endif
</div>

@if(session('success'))
    <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-800 rounded-lg">{{ session('success') }}</div>
@endif

<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    @forelse($notifications as $notification)
        @php $isRead = $notification->isReadBy(auth()->user()); @endphp
        <div class="border-b border-gray-100 last:border-0 {{ $isRead ? 'bg-gray-50/50' : 'bg-white' }}">
            <div class="px-6 py-4 flex items-start gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-semibold text-gray-900">{{ $notification->title }}</span>
                        <span class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</span>
                        @if(!$isRead)
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded">Mới</span>
                        @endif
                    </div>
                    @if($notification->message)
                        <p class="text-sm text-gray-600 mt-1">{{ $notification->message }}</p>
                    @endif
                </div>
                @if(!$isRead)
                    <form method="POST" action="{{ route('admin.notifications.mark-read', $notification) }}" class="flex-shrink-0">
                        @csrf
                        <button type="submit" class="text-xs text-red-600 hover:text-red-700 font-medium">Đã đọc</button>
                    </form>
                @endif
            </div>
        </div>
    @empty
        <div class="px-6 py-12 text-center text-gray-500">
            <p class="text-lg">Chưa có thông báo nào.</p>
        </div>
    @endforelse
</div>

@if($notifications->hasPages())
    <div class="mt-4">
        {{ $notifications->links() }}
    </div>
@endif
@endsection
