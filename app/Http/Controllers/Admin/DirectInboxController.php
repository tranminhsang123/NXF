<?php

namespace App\Http\Controllers\Admin;

use App\Events\DirectMessageSent;
use App\Http\Controllers\Controller;
use App\Models\DirectConversation;
use App\Models\DirectMessage;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DirectInboxController extends Controller
{
    private function chatWriteMode(): string
    {
        return (string) config('chat.write_mode', 'normal');
    }

    private function isUniqueConstraintViolation(QueryException $e): bool
    {
        return in_array($e->getCode(), ['23000', '23505'], true);
    }

    public function unreadCount(Request $request)
    {
        $admin = $request->user();

        $count = DirectMessage::query()
            ->where('recipient_id', $admin->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'unread_count' => $count,
        ]);
    }

    public function index(Request $request)
    {
        $admin = $request->user();

        $conversations = DirectConversation::query()
            ->where('admin_id', $admin->id)
            ->with(['user:id,name,email', 'messages' => function ($q) {
                $q->latest('id')->limit(1);
            }])
            ->withCount([
                'messages as unread_count' => fn ($q) => $q->where('recipient_id', $admin->id)->whereNull('read_at'),
            ])
            ->orderByDesc('last_message_at')
            ->get();

        $users = User::query()
            ->where('role', 'user')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('admin.inbox.index', [
            'conversations' => $conversations,
            'users' => $users,
        ]);
    }

    public function show(Request $request, User $user)
    {
        abort_if($user->role === 'admin', 404);
        $admin = $request->user();

        $conversation = DirectConversation::firstOrCreate(
            ['admin_id' => $admin->id, 'user_id' => $user->id],
            ['last_message_at' => null]
        );

        $conversations = DirectConversation::query()
            ->where('admin_id', $admin->id)
            ->with(['user:id,name,email', 'messages' => function ($q) {
                $q->latest('id')->limit(1);
            }])
            ->withCount([
                'messages as unread_count' => fn ($q) => $q->where('recipient_id', $admin->id)->whereNull('read_at'),
            ])
            ->orderByDesc('last_message_at')
            ->get();

        DirectMessage::query()
            ->where('conversation_id', $conversation->id)
            ->where('recipient_id', $admin->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = DirectMessage::query()
            ->where('conversation_id', $conversation->id)
            ->with('sender:id,name')
            ->orderByDesc('id')
            ->limit(100)
            ->get()
            ->reverse()
            ->values();

        return view('admin.inbox.show', [
            'conversation' => $conversation->load('user:id,name,email'),
            'conversations' => $conversations,
            'messages' => $messages,
            'users' => User::query()
                ->where('role', 'user')
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    }

    public function fetch(Request $request, DirectConversation $conversation)
    {
        $admin = $request->user();
        $this->authorize('view', $conversation);
        abort_unless((int) $conversation->admin_id === (int) $admin->id, 403);

        $afterId = (int) $request->query('after_id', 0);
        $beforeId = (int) $request->query('before_id', 0);

        DirectMessage::query()
            ->where('conversation_id', $conversation->id)
            ->where('recipient_id', $admin->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $query = DirectMessage::query()
            ->where('conversation_id', $conversation->id)
            ->with('sender:id,name');

        if ($beforeId > 0) {
            $query->where('id', '<', $beforeId)->orderByDesc('id')->limit(80);
            $messages = $query->get()->reverse()->values();
        } else {
            $query->when($afterId > 0, fn ($q) => $q->where('id', '>', $afterId))
                ->orderBy('id')
                ->limit(80);
            $messages = $query->get()->values();
        }

        return response()->json([
            'messages' => $messages->map(fn (DirectMessage $m) => [
                'id' => $m->id,
                'message_uuid' => $m->message_uuid,
                'event_id' => $m->event_id,
                'parent_event_id' => $m->parent_event_id,
                'status' => $m->event_status,
                'conversation_id' => $m->conversation_id,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender?->name,
                'recipient_id' => $m->recipient_id,
                'content' => $m->content,
                'read_at' => $m->read_at?->toIso8601String(),
                'created_at' => $m->created_at?->toIso8601String(),
            ]),
            'meta' => [
                'cursor' => [
                    'before_id' => $messages->first()?->id,
                    'after_id' => $messages->last()?->id,
                ],
            ],
        ]);
    }

    public function store(Request $request, User $user)
    {
        if ($this->chatWriteMode() === 'disable_write') {
            return response()->json([
                'error' => [
                    'code' => 'CHAT_DISABLED',
                    'message' => 'Chat is temporarily unavailable',
                ],
            ], 503);
        }

        abort_if($user->role === 'admin', 404);
        $admin = $request->user();

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
            'client_message_id' => ['nullable', 'string', 'max:100'],
            'parent_event_id' => ['nullable', 'uuid'],
        ]);

        $conversation = DirectConversation::firstOrCreate(
            ['admin_id' => $admin->id, 'user_id' => $user->id],
            ['last_message_at' => null]
        );
        $this->authorize('send', $conversation);

        $idempotent = false;
        $clientMessageId = $validated['client_message_id'] ?? null;
        $message = null;

        if ($clientMessageId) {
            $message = DirectMessage::query()
                ->where('sender_id', $admin->id)
                ->where('client_message_id', $clientMessageId)
                ->first();
            $idempotent = $message !== null;
        }

        if (! $message) {
            try {
                $message = DB::transaction(function () use ($conversation, $admin, $user, $validated, $clientMessageId) {
                    $created = DirectMessage::create([
                        'conversation_id' => $conversation->id,
                        'sender_id' => $admin->id,
                        'recipient_id' => $user->id,
                        'message_uuid' => (string) Str::uuid(),
                        'content' => $validated['content'],
                        'client_message_id' => $clientMessageId,
                        'event_id' => (string) Str::uuid(),
                        'parent_event_id' => $validated['parent_event_id'] ?? null,
                        'event_status' => 'pending',
                        'next_retry_at' => now(),
                    ]);
                    $conversation->update(['last_message_at' => $created->created_at]);

                    return $created;
                });
            } catch (QueryException $e) {
                if (! $clientMessageId || ! $this->isUniqueConstraintViolation($e)) {
                    throw $e;
                }
                $message = DirectMessage::query()
                    ->where('sender_id', $admin->id)
                    ->where('client_message_id', $clientMessageId)
                    ->firstOrFail();
                $idempotent = true;
            }
        }

        $message->loadMissing('sender:id,name');
        $mode = $this->chatWriteMode();

        if ($mode !== 'degrade_no_broadcast' && $message->event_status !== 'sent') {
            try {
                broadcast(new DirectMessageSent($message))->toOthers();
                $message->forceFill([
                    'event_status' => 'sent',
                    'next_retry_at' => null,
                    'event_last_error' => null,
                ])->save();
            } catch (\Throwable $e) {
                $nextCount = ((int) $message->event_retry_count) + 1;
                $isFinal = $nextCount >= 3;
                $message->forceFill([
                    'event_status' => $isFinal ? 'failed' : 'pending',
                    'event_retry_count' => $nextCount,
                    'next_retry_at' => $isFinal ? null : now()->addSeconds(2 ** $nextCount),
                    'event_last_error' => mb_substr($e->getMessage(), 0, 500),
                ])->save();
                Log::warning('admin.direct_message.broadcast_failed', [
                    'conversation_id' => $conversation->id,
                    'message_id' => $message->id,
                    'message_uuid' => $message->message_uuid,
                    'event_id' => $message->event_id,
                    'parent_event_id' => $message->parent_event_id,
                    'error' => $e->getMessage(),
                ]);
            }
        } elseif ($mode === 'degrade_no_broadcast') {
            $message->forceFill([
                'event_status' => 'failed',
                'next_retry_at' => null,
                'event_last_error' => 'broadcast_skipped_degrade_mode',
            ])->save();
        }

        return response()->json([
            'message' => [
                'id' => $message->id,
                'message_uuid' => $message->message_uuid,
                'event_id' => $message->event_id,
                'parent_event_id' => $message->parent_event_id,
                'status' => $message->event_status,
                'conversation_id' => $message->conversation_id,
                'sender_id' => $message->sender_id,
                'sender_name' => $message->sender?->name,
                'recipient_id' => $message->recipient_id,
                'content' => $message->content,
                'read_at' => $message->read_at?->toIso8601String(),
                'created_at' => $message->created_at?->toIso8601String(),
            ],
            'meta' => [
                'idempotent' => $idempotent,
                'mode' => $mode,
            ],
        ]);
    }
}
