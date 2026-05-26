@extends('adminlayout.app')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">Trung tâm hỗ trợ / kiểm duyệt</h1>
    <p class="text-gray-600 mt-2">Tập trung yêu cầu tham gia nhóm, inbox chưa đọc, chat gần đây và tín hiệu bảo mật.</p>
</div>

@if(session('success'))
    <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('success') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <p class="text-sm text-gray-500">Yêu cầu vào nhóm</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $pendingJoinRequests->count() }}</p>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <p class="text-sm text-gray-500">Inbox chưa đọc</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $unreadDirectMessages->count() }}</p>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <p class="text-sm text-gray-500">Chat gần đây</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $recentChatMessages->count() }}</p>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <p class="text-sm text-gray-500">Report chat</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $pendingReports->count() }}</p>
    </div>
    <div class="rounded-lg bg-white p-5 shadow-sm">
        <p class="text-sm text-gray-500">Tín hiệu DevTools</p>
        <p class="mt-2 text-3xl font-bold text-gray-900">{{ $devtoolsViolations->count() }}</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-sm p-6 xl:col-span-2">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Report chat đang chờ</h2>
        <div class="space-y-3">
            @forelse($pendingReports as $report)
                <div class="rounded-lg border border-amber-100 bg-amber-50 p-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $report->group?->name ?? 'Nhóm đã xoá' }} - {{ $report->message?->sender?->name ?? 'Người gửi' }}
                            </p>
                            <p class="mt-1 text-sm text-gray-700">{{ \Illuminate\Support\Str::limit($report->message?->content ?? 'Tin nhắn không còn tồn tại.', 220) }}</p>
                            <p class="mt-2 text-xs text-amber-900">
                                Người báo cáo: {{ $report->reporter?->name ?? 'Người dùng' }} - {{ $report->created_at->format('d/m/Y H:i') }}
                            </p>
                            <p class="mt-1 text-xs text-amber-900">Lý do: {{ $report->reason }}</p>
                        </div>
                        @adminCan('chat_groups.moderate')
                            <div class="w-full shrink-0 space-y-2 lg:w-72">
                                <form method="POST" action="{{ route('admin.support-moderation.reports.remove-message', $report) }}" class="space-y-2">
                                    @csrf
                                    <input name="resolution_note" class="w-full rounded-lg border border-amber-200 px-3 py-2 text-xs" placeholder="Ghi chú xử lý">
                                    <button class="w-full rounded bg-red-600 px-3 py-2 text-xs font-semibold text-white hover:bg-red-700">Xoá tin nhắn</button>
                                </form>
                                <form method="POST" action="{{ route('admin.support-moderation.reports.dismiss', $report) }}" class="space-y-2">
                                    @csrf
                                    <input name="resolution_note" class="w-full rounded-lg border border-amber-200 px-3 py-2 text-xs" placeholder="Lý do bỏ qua">
                                    <button class="w-full rounded bg-gray-700 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-800">Bỏ qua report</button>
                                </form>
                            </div>
                        @endadminCan
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Không có report chat đang chờ.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Yêu cầu tham gia đang chờ</h2>
        <div class="space-y-3">
            @forelse($pendingJoinRequests as $join)
                <div class="rounded-lg border border-gray-100 p-4">
                    <p class="font-semibold text-gray-900">{{ $join->user?->name }} muốn tham gia {{ $join->group?->name }}</p>
                    <p class="text-xs text-gray-500">{{ $join->user?->email }} - {{ $join->created_at->format('d/m/Y H:i') }}</p>
                    <div class="mt-3 flex gap-2">
                        @adminCan('chat_groups.moderate')
                            <form method="POST" action="{{ route('admin.chat.groups.join-requests.approve', $join) }}">@csrf<button class="rounded bg-green-600 px-3 py-1 text-xs font-semibold text-white">Duyệt</button></form>
                            <form method="POST" action="{{ route('admin.chat.groups.join-requests.decline', $join) }}">@csrf<button class="rounded bg-red-600 px-3 py-1 text-xs font-semibold text-white">Từ chối</button></form>
                        @endadminCan
                    </div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Không có yêu cầu chờ duyệt.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Tin nhắn trực tiếp chưa đọc</h2>
        <div class="space-y-3">
            @forelse($unreadDirectMessages as $message)
                <a href="{{ route('admin.inbox.show', $message->sender_id) }}" class="block rounded-lg border border-gray-100 p-4 hover:bg-gray-50">
                    <p class="font-semibold text-gray-900">{{ $message->sender?->name ?? 'Người dùng' }}</p>
                    <p class="mt-1 text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($message->content, 140) }}</p>
                    <p class="mt-2 text-xs text-gray-400">{{ $message->created_at->format('d/m/Y H:i') }}</p>
                </a>
            @empty
                <p class="text-sm text-gray-500">Không có tin nhắn chưa đọc.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Hội thoại gần đây</h2>
        <div class="space-y-3">
            @forelse($recentConversations as $conversation)
                <a href="{{ route('admin.inbox.show', $conversation->user_id) }}" class="flex items-center justify-between rounded-lg border border-gray-100 p-3 hover:bg-gray-50">
                    <div>
                        <p class="font-semibold text-gray-900">{{ $conversation->user?->name }}</p>
                        <p class="text-xs text-gray-500">{{ $conversation->last_message_at?->format('d/m/Y H:i') ?? 'Chưa có tin nhắn' }}</p>
                    </div>
                    @if($conversation->unread_count > 0)
                        <span class="rounded-full bg-red-600 px-2 py-1 text-xs font-bold text-white">{{ $conversation->unread_count }}</span>
                    @endif
                </a>
            @empty
                <p class="text-sm text-gray-500">Chưa có hội thoại.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Mẫu trả lời nhanh</h2>
        <div class="space-y-2">
            @foreach($cannedReplies as $reply)
                <div class="rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-700">{{ $reply }}</div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Tin nhắn chat gần đây</h2>
        <div class="space-y-3">
            @forelse($recentChatMessages as $message)
                <div class="rounded-lg border border-gray-100 p-3">
                    <p class="text-sm text-gray-900">{{ \Illuminate\Support\Str::limit($message->content, 160) }}</p>
                    <p class="mt-2 text-xs text-gray-500">{{ $message->group?->name }} - {{ $message->sender?->name }} - {{ $message->created_at->format('d/m/Y H:i') }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">Chưa có tin nhắn chat.</p>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-6">
        <h2 class="text-lg font-bold text-gray-900 mb-4">Tín hiệu DevTools</h2>
        <div class="space-y-3">
            @forelse($devtoolsViolations as $violation)
                <div class="rounded-lg border border-amber-100 bg-amber-50 p-3 text-sm text-amber-900">
                    <p class="font-semibold">{{ $violation->user?->name ?? 'Khách' }}</p>
                    <p class="text-xs">{{ $violation->created_at->format('d/m/Y H:i') }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500">Chưa có vi phạm gần đây.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
