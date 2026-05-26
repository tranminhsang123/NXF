<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat - {{ $group->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">

    <!-- Echo + Pusher (WebSocket real-time) -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1/dist/echo.iife.js"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    @include('layouts.header')

    <div class="container mx-auto px-4 max-w-6xl pt-8 pb-16">
        <div class="flex items-start gap-6">
            <!-- Sidebar: danh sách nhóm -->
            <aside class="hidden lg:block w-64 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="h-16 px-6 border-b border-gray-100 flex items-center justify-between">
                    <div class="text-gray-700 leading-tight">
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500">Nhóm chat</div>
                        <div class="text-sm font-semibold">{{ $userGroups->count() }} nhóm</div>
                    </div>
                    <div class="w-9 h-9 rounded-full bg-gray-100 text-gray-700 flex items-center justify-center text-xs font-bold">
                        {{ $userGroups->count() }}
                    </div>
                </div>
                <div class="max-h-[70vh] overflow-y-auto py-2">
                    @foreach($userGroups as $g)
                        @php
                            $last = $g->messages->first();
                            $active = $g->id === $group->id;
                        @endphp
                        <a href="{{ route('chat.show', ['group' => $g->id]) }}"
                           class="block px-4 py-3 text-sm {{ $active ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:bg-gray-50 hover:text-red-600' }}">
                            <div class="font-semibold truncate">{{ $g->name }}</div>
                            <div class="text-xs text-gray-400 truncate mt-0.5">
                                {{ $last?->content ?: 'Chưa có tin nhắn.' }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </aside>

            <!-- Khung chat -->
            <div class="flex-1">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <a href="{{ route('chat.index') }}" class="text-sm text-red-600 hover:text-red-700 lg:hidden">← Nhóm chat</a>
                        <!-- <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $group->name }}</h1> -->
                    </div>
                    <!-- Text only info removed (see chat composer) -->
                    <div class="text-xs text-gray-500 hidden"></div>
                </div>

        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-red-600 to-red-700 px-6 h-16">
                <div class="flex items-center justify-between gap-4 h-full">
                    <div class="text-white">
                        <div class="text-base font-semibold">{{ $group->name }}</div>
                    </div>
                    <div class="text-white/90 text-xs hidden">
                        Text only
                    </div>
                </div>
            </div>

            <div class="flex flex-col" style="height: 68vh;">
                <div id="messages"
                     class="flex-1 overflow-y-auto px-6 py-6 bg-gray-50"
                     data-group-id="{{ $group->id }}">
                @forelse($messages as $m)
                    @php
                        $isMe = $m->sender_id === auth()->id();
                        $senderName = $m->sender?->name ?? 'User';
                        $initial = mb_strtoupper(mb_substr($senderName, 0, 1));
                    @endphp
                    <div class="group flex w-full items-end mb-3 gap-2 {{ $isMe ? 'justify-end' : '' }}"
                         data-message-id="{{ $m->id }}"
                         data-sender-name="{{ $senderName }}"
                         data-content="{{ $m->content }}">
                        <div class="w-8 h-8 rounded-full bg-red-600 text-white flex items-center justify-center text-xs font-semibold flex-none {{ $isMe ? 'order-2 ml-2' : 'order-1 mr-2' }}">
                            {{ $initial }}
                        </div>
                        <div class="{{ $isMe ? 'bg-red-600 text-white order-1' : 'bg-white text-gray-900 border border-gray-200 order-2' }} relative rounded-2xl px-4 py-3 max-w-[88%] sm:max-w-[78%] shadow-sm">
                            <div class="pr-10">
                                <div class="text-[11px] font-semibold opacity-90">
                                    {{ $isMe ? 'Bạn' : $senderName }}
                                </div>
                            </div>
                            @if($m->repliedMessage)
                                <div class="{{ $isMe ? 'bg-white/10 border-white/35' : 'bg-gray-50 border-gray-200' }} mb-2 rounded-xl border px-3 py-2">
                                    <div class="text-[11px] font-semibold {{ $isMe ? 'text-white' : 'text-gray-700' }}">
                                        Trả lời {{ $m->repliedMessage->sender?->name ?? 'User' }}
                                    </div>
                                    <div class="text-xs {{ $isMe ? 'text-white/90' : 'text-gray-500' }} truncate">
                                        {{ $m->repliedMessage->content }}
                                    </div>
                                </div>
                            @endif
                            <div class="whitespace-pre-wrap break-words text-sm leading-relaxed">{{ $m->content }}</div>
                            <!-- time removed -->
                        </div>

                        <div class="relative {{ $isMe ? 'order-0 mr-1' : 'order-3 ml-1' }}" data-menu-wrap="1">
                            <button type="button"
                                    class="w-9 h-9 inline-flex items-center justify-center rounded-full bg-sky-100 text-sky-700 hover:bg-sky-200
                                           opacity-0 pointer-events-none lg:pointer-events-auto lg:opacity-0 lg:group-hover:opacity-100 transition-opacity
                                           {{ $isMe ? 'bg-white/10 text-white hover:bg-white/15' : '' }}"
                                    aria-label="Tùy chọn"
                                    data-menu-btn="1"
                                    data-message-id="{{ $m->id }}">
                                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <circle cx="12" cy="5" r="1.8"></circle>
                                    <circle cx="12" cy="12" r="1.8"></circle>
                                    <circle cx="12" cy="19" r="1.8"></circle>
                                </svg>
                            </button>
                            <div data-menu="1"
                                 class="hidden absolute {{ $isMe ? 'right-0' : 'left-0' }} mt-2 w-44 rounded-xl border border-gray-200 bg-white shadow-lg overflow-hidden z-20">
                                <button type="button"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50"
                                        onclick="window.__chatActions?.forward({{ $m->id }})">
                                    Chuyển tiếp
                                </button>
                                <button type="button"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50"
                                        onclick="window.__chatActions?.reply({{ $m->id }})">
                                    Trả lời
                                </button>
                                <button type="button"
                                        class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 text-amber-700"
                                        onclick="window.__chatActions?.report({{ $m->id }})">
                                    Báo cáo
                                </button>
                                @if($isMe)
                                    <button type="button"
                                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50"
                                            onclick="window.__chatActions?.edit({{ $m->id }})">
                                        Sửa
                                    </button>
                                    <button type="button"
                                            class="w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 text-red-600"
                                            onclick="window.__chatActions?.del({{ $m->id }})">
                                        Xóa
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="h-full flex items-center justify-center">
                        <div class="text-center">
                            <div class="text-gray-500 font-semibold">Chưa có tin nhắn</div>
                            <div class="text-xs text-gray-400 mt-1">Hãy gửi tin nhắn đầu tiên!</div>
                        </div>
                    </div>
                @endforelse
                </div>

                <form id="chat-form" class="p-4 bg-white border-t border-gray-200">
                    <div id="replying-box" class="hidden mb-3 rounded-xl border border-red-200 bg-red-50 px-3 py-2 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="text-[11px] font-semibold text-red-700">Đang trả lời <span id="replying-name"></span></div>
                                <div id="replying-content" class="text-xs text-red-600 truncate"></div>
                            </div>
                            <button id="replying-cancel" type="button" class="text-xs font-semibold text-red-600 hover:text-red-700">Hủy</button>
                        </div>
                    </div>
                    <div class="flex gap-2 sm:gap-3">
                        <input id="chat-input"
                               type="text"
                               class="flex-1 border border-gray-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-red-500"
                               placeholder="Nhập tin nhắn..."
                               autocomplete="off">
                        <button id="chat-send"
                                type="submit"
                                class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-3 rounded-xl disabled:opacity-60 disabled:cursor-not-allowed">
                            <span class="hidden sm:inline">Gửi</span>
                            <span class="sm:hidden">➤</span>
                        </button>
                    </div>
                    <!-- Hint removed -->
                </form>
            </div>
        </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const currentUserId = @json(auth()->id());
            const currentUserName = @json(auth()->user()->name ?? 'Bạn');
            const groupId = @json($group->id);

            const fetchUrlBase = @json(route('chat.messages.fetch', ['group' => $group->id]));
            const storeUrl = @json(route('chat.messages.store', ['group' => $group->id]));
            const updateUrlBase = @json(route('chat.messages.update', ['message' => 0]));
            const deleteUrlBase = @json(route('chat.messages.destroy', ['message' => 0]));
            const forwardUrlBase = @json(route('chat.messages.forward', ['message' => 0]));
            const reportUrlBase = @json(route('chat.messages.report', ['message' => 0]));
            const userGroups = @json(($userGroups ?? collect())->map(fn($g) => ['id' => $g->id, 'name' => $g->name])->values());
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const messagesEl = document.getElementById('messages');
            const chatForm = document.getElementById('chat-form');
            const chatInput = document.getElementById('chat-input');
            const chatSendBtn = document.getElementById('chat-send');
            const replyingBox = document.getElementById('replying-box');
            const replyingName = document.getElementById('replying-name');
            const replyingContent = document.getElementById('replying-content');
            const replyingCancelBtn = document.getElementById('replying-cancel');

            let seenMessageIds = new Set();
            let firstMessageId = null;
            let lastMessageId = null;
            let replyingTo = null;

            // Init IDs from DOM
            Array.from(messagesEl.querySelectorAll('[data-message-id]')).forEach((node) => {
                const id = parseInt(node.getAttribute('data-message-id'), 10);
                if (!Number.isFinite(id)) return;
                seenMessageIds.add(id);
                if (firstMessageId === null) firstMessageId = id;
                lastMessageId = id;
            });

            function escapeForText(text) {
                // We use textContent instead of innerHTML to avoid XSS
                return text ?? '';
            }

            // Expose actions for server-rendered buttons
            window.__chatActions = {
                edit: onEdit,
                del: onDelete,
                forward: onForward,
                reply: onReply,
                report: onReport,
            };

            function closeAllMenus() {
                document.querySelectorAll('[data-menu="1"]').forEach((el) => el.classList.add('hidden'));
            }

            function isDesktopView() {
                return window.matchMedia('(min-width: 1024px)').matches;
            }

            function hideMobileActionButtons(exceptBtn = null) {
                if (isDesktopView()) return;
                document.querySelectorAll('[data-menu-btn="1"]').forEach((btn) => {
                    if (exceptBtn && btn === exceptBtn) return;
                    btn.style.opacity = '';
                    btn.style.pointerEvents = '';
                });
            }

            function showMobileActionButton(btn) {
                if (!btn || isDesktopView()) return;
                hideMobileActionButtons(btn);
                btn.style.opacity = '1';
                btn.style.pointerEvents = 'auto';
            }

            document.addEventListener('click', (e) => {
                const btn = e.target?.closest?.('[data-menu-btn="1"]');
                if (!btn) {
                    closeAllMenus();
                    hideMobileActionButtons();
                    return;
                }

                e.preventDefault();
                e.stopPropagation();
                const wrapper = btn.closest?.('[data-menu-wrap="1"]') || btn.parentElement;
                const menu = wrapper?.querySelector?.('[data-menu="1"]');
                if (!menu) return;

                const wasOpen = !menu.classList.contains('hidden');
                closeAllMenus();
                if (!wasOpen) menu.classList.remove('hidden');
            });

            let longPressTimer = null;

            document.addEventListener('touchstart', (e) => {
                if (isDesktopView()) return;
                const row = e.target?.closest?.('[data-message-id]');
                if (!row) return;

                const btn = row.querySelector?.('[data-menu-btn="1"]');
                if (!btn) return;

                clearTimeout(longPressTimer);
                longPressTimer = setTimeout(() => {
                    showMobileActionButton(btn);
                }, 450);
            }, { passive: true });

            document.addEventListener('touchmove', () => {
                clearTimeout(longPressTimer);
            }, { passive: true });

            document.addEventListener('touchend', () => {
                clearTimeout(longPressTimer);
            }, { passive: true });

            window.addEventListener('resize', () => {
                if (!isDesktopView()) return;
                document.querySelectorAll('[data-menu-btn="1"]').forEach((btn) => {
                    btn.style.opacity = '';
                    btn.style.pointerEvents = '';
                });
            });

            function buildMessageNode(msg) {
                const isMe = parseInt(msg.sender_id, 10) === parseInt(currentUserId, 10);

                const row = document.createElement('div');
                row.className = 'group flex w-full items-end mb-3 gap-2 ' + (isMe ? 'justify-end' : '');
                row.setAttribute('data-message-id', msg.id);
                row.setAttribute('data-sender-name', isMe ? currentUserName : (msg.sender_name || 'User'));
                row.setAttribute('data-content', msg.content ?? '');

                const avatar = document.createElement('div');
                avatar.className = 'w-8 h-8 rounded-full bg-red-600 text-white flex-none flex items-center justify-center text-xs font-semibold ' + (isMe ? 'order-2 ml-2' : 'order-1 mr-2');
                const senderName = isMe ? currentUserName : (msg.sender_name || 'User');
                const initial = senderName ? senderName.trim().charAt(0).toUpperCase() : 'U';
                avatar.textContent = initial;

                const bubble = document.createElement('div');
                bubble.className = (isMe ? 'bg-red-600 text-white order-1' : 'bg-white text-gray-900 border border-gray-200 order-2') + ' relative rounded-2xl px-4 py-3 max-w-[88%] sm:max-w-[78%] shadow-sm';
                bubble.setAttribute('data-bubble', '1');
                bubble.setAttribute('data-content', msg.content ?? '');

                const sender = document.createElement('div');
                sender.className = 'text-[11px] font-semibold opacity-90';
                sender.textContent = isMe ? 'Bạn' : senderName;

                const header = document.createElement('div');
                header.className = 'pr-2';
                header.appendChild(sender);

                let replyBox = null;
                if (msg.reply_to && msg.reply_to.id) {
                    replyBox = document.createElement('div');
                    replyBox.className = (isMe ? 'bg-white/10 border-white/35' : 'bg-gray-50 border-gray-200') + ' mb-2 rounded-xl border px-3 py-2';

                    const replyName = document.createElement('div');
                    replyName.className = 'text-[11px] font-semibold ' + (isMe ? 'text-white' : 'text-gray-700');
                    replyName.textContent = 'Trả lời ' + (msg.reply_to.sender_name || 'User');

                    const replyContent = document.createElement('div');
                    replyContent.className = 'text-xs truncate ' + (isMe ? 'text-white/90' : 'text-gray-500');
                    replyContent.textContent = escapeForText(msg.reply_to.content || '');

                    replyBox.appendChild(replyName);
                    replyBox.appendChild(replyContent);
                }

                const menuWrap = document.createElement('div');
                menuWrap.className = 'relative ' + (isMe ? 'order-0 mr-1' : 'order-3 ml-1');
                menuWrap.setAttribute('data-menu-wrap', '1');

                const menuBtn = document.createElement('button');
                menuBtn.type = 'button';
                menuBtn.className = 'w-9 h-9 inline-flex items-center justify-center rounded-full opacity-0 pointer-events-none lg:pointer-events-auto lg:opacity-0 lg:group-hover:opacity-100 transition-opacity ' +
                    (isMe ? 'bg-white/10 text-white hover:bg-white/15' : 'bg-sky-100 text-sky-700 hover:bg-sky-200');
                menuBtn.setAttribute('aria-label', 'Tùy chọn');
                menuBtn.setAttribute('data-menu-btn', '1');
                menuBtn.setAttribute('data-message-id', String(msg.id));
                menuBtn.innerHTML = '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><circle cx="12" cy="5" r="1.8"></circle><circle cx="12" cy="12" r="1.8"></circle><circle cx="12" cy="19" r="1.8"></circle></svg>';

                const menu = document.createElement('div');
                menu.setAttribute('data-menu', '1');
                menu.className = 'hidden absolute ' + (isMe ? 'right-0' : 'left-0') + ' mt-2 w-44 rounded-xl border border-gray-200 bg-white shadow-lg overflow-hidden z-20';

                const itemForward = document.createElement('button');
                itemForward.type = 'button';
                itemForward.className = 'w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50';
                itemForward.textContent = 'Chuyển tiếp';
                itemForward.addEventListener('click', () => { closeAllMenus(); onForward(msg.id); });
                menu.appendChild(itemForward);

                const itemReply = document.createElement('button');
                itemReply.type = 'button';
                itemReply.className = 'w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50';
                itemReply.textContent = 'Trả lời';
                itemReply.addEventListener('click', () => { closeAllMenus(); onReply(msg.id); });
                menu.appendChild(itemReply);

                const itemReport = document.createElement('button');
                itemReport.type = 'button';
                itemReport.className = 'w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 text-amber-700';
                itemReport.textContent = 'Báo cáo';
                itemReport.addEventListener('click', () => { closeAllMenus(); onReport(msg.id); });
                menu.appendChild(itemReport);

                if (isMe) {
                    const itemEdit = document.createElement('button');
                    itemEdit.type = 'button';
                    itemEdit.className = 'w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50';
                    itemEdit.textContent = 'Sửa';
                    itemEdit.addEventListener('click', () => { closeAllMenus(); onEdit(msg.id); });
                    menu.appendChild(itemEdit);

                    const itemDelete = document.createElement('button');
                    itemDelete.type = 'button';
                    itemDelete.className = 'w-full text-left px-4 py-2.5 text-sm hover:bg-gray-50 text-red-600';
                    itemDelete.textContent = 'Xóa';
                    itemDelete.addEventListener('click', () => { closeAllMenus(); onDelete(msg.id); });
                    menu.appendChild(itemDelete);
                }

                const content = document.createElement('div');
                content.className = 'whitespace-pre-wrap break-words text-sm';
                content.textContent = escapeForText(msg.content);

                // time removed (mobile-first UI)

                bubble.appendChild(header);
                if (replyBox) bubble.appendChild(replyBox);
                bubble.appendChild(content);
                // bubble.appendChild(time);
                row.appendChild(avatar);
                row.appendChild(bubble);
                menuWrap.appendChild(menuBtn);
                menuWrap.appendChild(menu);
                row.appendChild(menuWrap);
                return row;
            }

            function buildUrl(base, id) {
                return base.replace('/0', '/' + String(id));
            }

            function getMessageNodeById(id) {
                return messagesEl.querySelector(`[data-message-id="${id}"]`);
            }

            function setReplyingState(nextReplyingTo) {
                replyingTo = nextReplyingTo;
                if (!replyingTo) {
                    replyingBox?.classList.add('hidden');
                    if (replyingName) replyingName.textContent = '';
                    if (replyingContent) replyingContent.textContent = '';
                    return;
                }

                if (replyingName) replyingName.textContent = replyingTo.sender_name || 'User';
                if (replyingContent) replyingContent.textContent = replyingTo.content || '';
                replyingBox?.classList.remove('hidden');
            }

            function onReply(messageId) {
                const node = getMessageNodeById(messageId);
                if (!node) return;
                const senderName = node.getAttribute('data-sender-name') || 'User';
                const content = node.getAttribute('data-content') || '';
                setReplyingState({
                    id: messageId,
                    sender_name: senderName,
                    content: content,
                });
                chatInput?.focus();
            }

            async function onEdit(messageId) {
                const node = getMessageNodeById(messageId);
                const bubble = node?.querySelector('[data-bubble="1"]');
                const current = bubble?.getAttribute('data-content') || '';
                const next = prompt('Sửa tin nhắn:', current);
                if (next === null) return;
                const content = String(next).trim();
                if (!content) return;

                const url = buildUrl(updateUrlBase, messageId);
                const res = await fetch(url, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ content })
                });
                if (!res.ok) {
                    alert('Không sửa được tin nhắn.');
                    return;
                }
                const data = await res.json();
                const msg = data.message;
                if (!msg) return;

                // simplest: reload node by replacing content + edited flag
                if (bubble) bubble.setAttribute('data-content', msg.content ?? '');
                const contentEl = bubble?.querySelector('.whitespace-pre-wrap');
                if (contentEl) contentEl.textContent = msg.content ?? '';
                // time removed
            }

            async function onDelete(messageId) {
                if (!confirm('Xóa tin nhắn này?')) return;
                const url = buildUrl(deleteUrlBase, messageId);
                const res = await fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) {
                    alert('Không xóa được tin nhắn.');
                    return;
                }
                const node = getMessageNodeById(messageId);
                if (node) node.remove();
            }

            async function onForward(messageId) {
                if (!Array.isArray(userGroups) || userGroups.length === 0) {
                    alert('Bạn chưa có nhóm nào để chuyển tiếp.');
                    return;
                }
                const list = userGroups.map(g => `${g.id}: ${g.name}`).join('\n');
                const input = prompt('Chuyển tiếp tới nhóm (nhập ID):\n' + list, String(groupId));
                if (input === null) return;
                const targetGroupId = parseInt(input, 10);
                if (!Number.isFinite(targetGroupId)) return;

                const url = buildUrl(forwardUrlBase, messageId);
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ target_group_id: targetGroupId })
                });
                if (!res.ok) {
                    alert('Không chuyển tiếp được.');
                    return;
                }
                alert('Đã chuyển tiếp.');
            }

            async function onReport(messageId) {
                const reason = prompt('Lý do báo cáo tin nhắn này:', 'Tin nhắn không phù hợp');
                if (reason === null) return;
                const trimmed = String(reason).trim();
                if (!trimmed) return;

                const url = buildUrl(reportUrlBase, messageId);
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ reason: trimmed })
                });

                if (!res.ok) {
                    alert('Không gửi được báo cáo.');
                    return;
                }

                alert('Đã gửi báo cáo cho admin.');
            }

            replyingCancelBtn?.addEventListener('click', () => {
                setReplyingState(null);
                chatInput?.focus();
            });

            function scrollToBottom() {
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            function isNearBottom() {
                return (messagesEl.scrollTop + messagesEl.clientHeight) >= (messagesEl.scrollHeight - 200);
            }

            function appendMessages(messages, { prepend = false } = {}) {
                if (!Array.isArray(messages) || messages.length === 0) return;

                const nodes = [];
                const hasExistingMessages = messagesEl.querySelector('[data-message-id]') !== null;
                // Nếu đang ở trạng thái placeholder (chưa có tin nhắn thật), xóa để danh sách hiển thị đúng.
                if (!hasExistingMessages) {
                    messagesEl.innerHTML = '';
                }
                for (const msg of messages) {
                    const msgId = parseInt(msg.id, 10);
                    if (!Number.isFinite(msgId)) continue;
                    if (seenMessageIds.has(msgId)) continue;
                    seenMessageIds.add(msgId);
                    nodes.push(buildMessageNode(msg));
                }

                if (nodes.length === 0) return;

                if (prepend) {
                    nodes.reverse().forEach((node) => {
                        messagesEl.insertBefore(node, messagesEl.firstChild);
                    });
                } else {
                    nodes.forEach((node) => messagesEl.appendChild(node));
                }

                // Update first/last IDs
                const all = Array.from(messagesEl.querySelectorAll('[data-message-id]'));
                if (all.length) {
                    firstMessageId = parseInt(all[0].getAttribute('data-message-id'), 10);
                    lastMessageId = parseInt(all[all.length - 1].getAttribute('data-message-id'), 10);
                }
            }

            // Send message
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                const content = (chatInput.value || '').trim();
                if (!content) return;
                if (chatSendBtn.disabled) return;

                chatSendBtn.disabled = true;
                try {
                    const res = await fetch(storeUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            content,
                            reply_to_message_id: replyingTo?.id ?? null
                        })
                    });
                    if (!res.ok) {
                        let bodyText = '';
                        try { bodyText = await res.text(); } catch (e) {}
                        console.error('chat.send failed', { status: res.status, body: bodyText?.slice(0, 500) });
                        throw new Error(bodyText ? bodyText.slice(0, 200) : 'send failed');
                    }

                    const data = await res.json();
                    const msg = data.message;
                    if (msg && msg.id) {
                        const nearBottom = isNearBottom();
                        appendMessages([msg], { prepend: false });
                        if (nearBottom) scrollToBottom();
                    }

                    chatInput.value = '';
                    setReplyingState(null);
                    chatInput.focus();
                } catch (err) {
                    alert('Không gửi được tin nhắn. Vui lòng thử lại.');
                } finally {
                    chatSendBtn.disabled = false;
                }
            });

            // Load older messages when scroll to top
            let loadingMore = false;
            messagesEl.addEventListener('scroll', async () => {
                if (loadingMore) return;
                if (messagesEl.scrollTop > 0) return;
                if (!firstMessageId) return;

                loadingMore = true;
                const oldScrollTop = messagesEl.scrollTop;
                const oldScrollHeight = messagesEl.scrollHeight;

                try {
                    const url = `${fetchUrlBase}?before_id=${firstMessageId}&limit=50`;
                    const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                    if (!res.ok) throw new Error('load-more failed');
                    const data = await res.json();
                    const older = data.messages || [];
                    const hasAny = older.length > 0;

                    if (hasAny) {
                        appendMessages(older, { prepend: true });
                        // Keep viewport stable after prepending
                        const newScrollHeight = messagesEl.scrollHeight;
                        messagesEl.scrollTop = oldScrollTop + (newScrollHeight - oldScrollHeight);
                    }
                } catch (err) {
                    // silently ignore
                } finally {
                    loadingMore = false;
                }
            });

            // WebSocket real-time via Echo
            const pusherKey = @json(env('PUSHER_APP_KEY'));
            const pusherCluster = @json(env('PUSHER_APP_CLUSTER'));
            const broadcastDriver = @json(env('BROADCAST_DRIVER'));
            let echoReady = false;

            function startPolling() {
                // Fallback: polling every 3s if Echo không kết nối / BROADCAST_DRIVER không bật
                const interval = setInterval(async () => {
                    try {
                        const url = lastMessageId
                            ? `${fetchUrlBase}?after_id=${lastMessageId}&limit=50`
                            : `${fetchUrlBase}?limit=50`;
                        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        if (!res.ok) return;
                        const data = await res.json();
                        const incoming = data.messages || [];
                        const nearBottom = isNearBottom();
                        appendMessages(incoming, { prepend: false });
                        if (incoming.length && nearBottom) scrollToBottom();
                    } catch (err) {
                        // ignore
                    }
                }, 3000);
                return interval;
            }

            let pollTimer = startPolling();

            if (pusherKey) {
                try {
                    window.Echo = new Echo({
                        broadcaster: 'pusher',
                        key: pusherKey,
                        cluster: pusherCluster,
                        encrypted: true,
                        authEndpoint: '/broadcasting/auth',
                        auth: {
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                            }
                        }
                    });

                    const channelName = `chat-group.${groupId}`;
                    const channel = window.Echo.private(channelName);

                    channel.listen('.chat.message.sent', (e) => {
                        // e should match broadcastWith() payload
                        const nearBottom = isNearBottom();
                        appendMessages([e], { prepend: false });
                        if (nearBottom) scrollToBottom();
                    });

                    window.Echo.connector.pusher.connection.bind('connected', () => {
                        echoReady = true;
                        if (pollTimer && broadcastDriver === 'pusher') clearInterval(pollTimer);
                    });
                } catch (err) {
                    // keep polling
                }
            }

            // If we didn't start messages from DOM (empty chat), scroll to bottom
            scrollToBottom();
        })();
    </script>
</body>
</html>
