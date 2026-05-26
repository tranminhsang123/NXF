<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox hỗ trợ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1/dist/echo.iife.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
@include('layouts.header')
<div class="container mx-auto px-4 max-w-6xl pt-8 pb-12">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <aside class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-100">
                <h2 class="text-sm font-semibold text-gray-800">Hộp thư hỗ trợ</h2>
            </div>
            <div class="max-h-[68vh] overflow-y-auto">
                @forelse($conversations as $c)
                    @php $last = $c->messages->first(); @endphp
                    <a href="{{ route('inbox.show', ['conversation' => $c->id]) }}" class="block px-4 py-3 border-b border-gray-100 hover:bg-gray-50 {{ optional($conversation)->id === $c->id ? 'bg-red-50' : '' }}">
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-sm font-semibold text-gray-900 truncate">{{ $c->admin?->name ?? 'Admin' }}</div>
                            @if(($c->unread_count ?? 0) > 0)
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-red-100 text-red-700">{{ $c->unread_count }}</span>
                            @endif
                        </div>
                        <div class="text-xs text-gray-500 truncate mt-1">{{ $last?->content ?? 'Chưa có tin nhắn' }}</div>
                    </a>
                @empty
                    <div class="px-4 py-8 text-sm text-gray-500">Chưa có hội thoại nào với admin.</div>
                @endforelse
            </div>
        </aside>

        <section class="lg:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            @if(!$conversation)
                <div class="h-[68vh] flex items-center justify-center text-gray-500">Chọn một hội thoại để bắt đầu.</div>
            @else
                <div class="h-16 px-5 bg-gradient-to-r from-red-600 to-red-700 text-white flex items-center">
                    <div>
                        <div class="text-xs opacity-90">Trao đổi riêng</div>
                        <div class="text-base font-semibold">{{ $conversation->admin?->name ?? 'Admin' }}</div>
                    </div>
                </div>
                <div id="inbox-messages" class="h-[56vh] overflow-y-auto bg-gray-50 px-5 py-4" data-conversation-id="{{ $conversation->id }}">
                    @foreach($messages as $m)
                        @php $isMe = (int)$m->sender_id === (int)auth()->id(); @endphp
                        <div class="mb-3 flex {{ $isMe ? 'justify-end' : 'justify-start' }}" data-message-id="{{ $m->id }}">
                            <div class="max-w-[80%] rounded-2xl px-4 py-3 {{ $isMe ? 'bg-red-600 text-white' : 'bg-white text-gray-900 border border-gray-200' }}">
                                <div class="text-[11px] font-semibold opacity-90">{{ $isMe ? 'Bạn' : ($m->sender?->name ?? 'Admin') }}</div>
                                <div class="text-sm whitespace-pre-wrap break-words">{{ $m->content }}</div>
                                @if($isMe)
                                    <div class="text-[10px] mt-1 {{ $m->read_at ? 'text-green-100' : 'text-white/70' }}">{{ $m->read_at ? 'Đã đọc' : 'Đã gửi' }}</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <form id="inbox-form" class="border-t border-gray-200 p-4">
                    <div class="flex gap-2">
                        <input id="inbox-input" type="text" class="flex-1 border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-500" placeholder="Nhắn tin cho admin..." autocomplete="off">
                        <button id="inbox-send" type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2.5 rounded-xl">Gửi</button>
                    </div>
                </form>
            @endif
        </section>
    </div>
</div>

@if($conversation)
<script>
(function () {
    const currentUserId = @json(auth()->id());
    const conversationId = @json($conversation->id);
    const fetchUrlBase = @json(route('inbox.messages.fetch', ['conversation' => $conversation->id]));
    const storeUrl = @json(route('inbox.messages.store', ['conversation' => $conversation->id]));
    const csrfToken = @json(csrf_token());
    const pusherKey = @json(env('PUSHER_APP_KEY'));
    const pusherCluster = @json(env('PUSHER_APP_CLUSTER'));

    const messagesEl = document.getElementById('inbox-messages');
    const formEl = document.getElementById('inbox-form');
    const inputEl = document.getElementById('inbox-input');
    const sendBtn = document.getElementById('inbox-send');

    let lastMessageId = 0;
    Array.from(messagesEl.querySelectorAll('[data-message-id]')).forEach((n) => {
        const id = parseInt(n.getAttribute('data-message-id') || '0', 10);
        if (id > lastMessageId) lastMessageId = id;
    });

    function scrollBottom() { messagesEl.scrollTop = messagesEl.scrollHeight; }
    function nearBottom() { return messagesEl.scrollTop + messagesEl.clientHeight >= messagesEl.scrollHeight - 120; }

    function appendMessage(msg) {
        if (!msg || !msg.id) return;
        if (messagesEl.querySelector('[data-message-id="' + msg.id + '"]')) return;
        const isMe = parseInt(msg.sender_id, 10) === parseInt(currentUserId, 10);
        const row = document.createElement('div');
        row.className = 'mb-3 flex ' + (isMe ? 'justify-end' : 'justify-start');
        row.setAttribute('data-message-id', String(msg.id));
        row.innerHTML = '<div class="max-w-[80%] rounded-2xl px-4 py-3 ' + (isMe ? 'bg-red-600 text-white' : 'bg-white text-gray-900 border border-gray-200') + '">' +
            '<div class="text-[11px] font-semibold opacity-90">' + (isMe ? 'Bạn' : (msg.sender_name || 'Admin')) + '</div>' +
            '<div class="text-sm whitespace-pre-wrap break-words"></div>' +
            (isMe ? '<div class="text-[10px] mt-1 ' + (msg.read_at ? 'text-green-100' : 'text-white/70') + '">' + (msg.read_at ? 'Đã đọc' : 'Đã gửi') + '</div>' : '') +
            '</div>';
        row.querySelector('.whitespace-pre-wrap').textContent = msg.content || '';
        messagesEl.appendChild(row);
        if (msg.id > lastMessageId) lastMessageId = msg.id;
    }

    async function fetchIncoming() {
        try {
            const res = await fetch(fetchUrlBase + '?after_id=' + lastMessageId, { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            const incoming = data.messages || [];
            const shouldScroll = nearBottom();
            incoming.forEach(appendMessage);
            if (incoming.length && shouldScroll) scrollBottom();
        } catch (e) {}
    }

    formEl.addEventListener('submit', async (e) => {
        e.preventDefault();
        const content = (inputEl.value || '').trim();
        if (!content || sendBtn.disabled) return;
        sendBtn.disabled = true;
        try {
            const res = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ content })
            });
            if (!res.ok) throw new Error('send_failed');
            const data = await res.json();
            appendMessage(data.message);
            inputEl.value = '';
            scrollBottom();
        } catch (e) {
            alert('Không gửi được tin nhắn.');
        } finally {
            sendBtn.disabled = false;
        }
    });

    let pollTimer = setInterval(fetchIncoming, 3000);
    if (pusherKey) {
        try {
            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: pusherKey,
                cluster: pusherCluster,
                encrypted: true,
                authEndpoint: '/broadcasting/auth',
                auth: { headers: { 'X-CSRF-TOKEN': csrfToken } }
            });
            window.Echo.private('App.Models.User.' + String(currentUserId))
                .listen('.direct.message.sent', (e) => {
                    if (parseInt(e.conversation_id, 10) !== parseInt(conversationId, 10)) return;
                    const shouldScroll = nearBottom();
                    appendMessage(e);
                    if (shouldScroll) scrollBottom();
                });
        } catch (e) {}
    }

    scrollBottom();
})();
</script>
@endif
</body>
</html>

