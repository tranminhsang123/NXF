<?php

namespace App\Http\Controllers;

use App\Events\ChatMessageSent;
use App\Models\ChatGroup;
use App\Models\ChatGroupMember;
use App\Models\ChatMessage;
use App\Models\ChatMessageReport;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatMessageController extends Controller
{
    private function ensureMember(Request $request, int $groupId): void
    {
        $user = $request->user();
        $isMember = ChatGroupMember::query()
            ->where('group_id', $groupId)
            ->where('user_id', $user->id)
            ->exists();

        if (! $isMember) {
            abort(403, 'Bạn không có quyền truy cập cuộc chat này.');
        }
    }

    private function chatWriteMode(): string
    {
        return (string) config('chat.write_mode', 'normal');
    }

    private function chatWriteDisabledResponse(): JsonResponse
    {
        return response()->json([
            'error' => [
                'code' => 'CHAT_DISABLED',
                'message' => 'Chat is temporarily unavailable',
            ],
        ], 503);
    }

    private function formatMessage(ChatMessage $message): array
    {
        return [
            'id' => $message->id,
            'message_uuid' => $message->message_uuid,
            'event_id' => $message->event_id,
            'parent_event_id' => $message->parent_event_id,
            'status' => $message->event_status,
            'group_id' => $message->group_id,
            'sender_id' => $message->sender_id,
            'sender_name' => $message->sender?->name,
            'content' => $message->content,
            'reply_to' => $message->repliedMessage ? [
                'id' => $message->repliedMessage->id,
                'sender_name' => $message->repliedMessage->sender?->name,
                'content' => $message->repliedMessage->content,
            ] : null,
            'edited_at' => $message->edited_at?->toIso8601String(),
            'is_forwarded' => (bool) ($message->forwarded_from_message_id || $message->forwarded_from_group_id),
            'forwarded_from_sender_name' => $message->forwarded_from_sender_name,
            'created_at' => $message->created_at?->toIso8601String(),
        ];
    }

    private function isUniqueConstraintViolation(QueryException $e): bool
    {
        return in_array($e->getCode(), ['23000', '23505'], true);
    }

    public function fetch(Request $request, ChatGroup $group)
    {
        $this->authorize('view', $group);
        $this->ensureMember($request, $group->id);

        $limit = (int) $request->query('limit', 50);
        $limit = max(1, min($limit, 100));

        $beforeId = $request->query('before_id');
        $afterId = $request->query('after_id');

        $query = ChatMessage::query()
            ->where('group_id', $group->id)
            ->with(['sender', 'repliedMessage.sender']);

        if ($beforeId !== null) {
            $query->where('id', '<', (int) $beforeId)->orderByDesc('id')->limit($limit);
            $messages = $query->get()->reverse()->values();
        } elseif ($afterId !== null) {
            $query->where('id', '>', (int) $afterId)->orderBy('id')->limit($limit);
            $messages = $query->get()->values();
        } else {
            $query->orderByDesc('id')->limit($limit);
            $messages = $query->get()->reverse()->values();
        }

        return response()->json([
            'messages' => $messages->map(fn (ChatMessage $m) => [
                'id' => $m->id,
                'message_uuid' => $m->message_uuid,
                'event_id' => $m->event_id,
                'parent_event_id' => $m->parent_event_id,
                'status' => $m->event_status,
                'group_id' => $m->group_id,
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender?->name,
                'content' => $m->content,
                'reply_to' => $m->repliedMessage ? [
                    'id' => $m->repliedMessage->id,
                    'sender_name' => $m->repliedMessage->sender?->name,
                    'content' => $m->repliedMessage->content,
                ] : null,
                'edited_at' => $m->edited_at?->toIso8601String(),
                'is_forwarded' => (bool) ($m->forwarded_from_message_id || $m->forwarded_from_group_id),
                'forwarded_from_sender_name' => $m->forwarded_from_sender_name,
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

    public function store(Request $request, ChatGroup $group)
    {
        if ($this->chatWriteMode() === 'disable_write') {
            return $this->chatWriteDisabledResponse();
        }

        $user = $request->user();
        $this->authorize('send', $group);
        $this->ensureMember($request, $group->id);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
            'reply_to_message_id' => ['nullable', 'integer'],
            'client_message_id' => ['nullable', 'string', 'max:100'],
            'parent_event_id' => ['nullable', 'uuid'],
        ]);

        $replyToMessageId = null;
        if (isset($validated['reply_to_message_id'])) {
            $candidateId = (int) $validated['reply_to_message_id'];
            if ($candidateId > 0) {
                $replyExistsInGroup = ChatMessage::query()
                    ->where('group_id', $group->id)
                    ->where('id', $candidateId)
                    ->exists();
                if (! $replyExistsInGroup) {
                    return response()->json([
                        'message' => 'Tin nhắn được trả lời không hợp lệ.',
                    ], 422);
                }
                $replyToMessageId = $candidateId;
            }
        }

        $idempotent = false;
        $clientMessageId = $validated['client_message_id'] ?? null;
        $message = null;

        if ($clientMessageId) {
            $message = ChatMessage::query()
                ->where('sender_id', $user->id)
                ->where('client_message_id', $clientMessageId)
                ->first();
            $idempotent = $message !== null;
        }

        if (! $message) {
            try {
                $message = DB::transaction(function () use ($group, $user, $validated, $replyToMessageId, $clientMessageId) {
                    return ChatMessage::create([
                        'group_id' => $group->id,
                        'sender_id' => $user->id,
                        'message_uuid' => (string) Str::uuid(),
                        'content' => $validated['content'],
                        'client_message_id' => $clientMessageId,
                        'event_id' => (string) Str::uuid(),
                        'parent_event_id' => $validated['parent_event_id'] ?? null,
                        'event_status' => 'pending',
                        'next_retry_at' => now(),
                        'reply_to_message_id' => $replyToMessageId,
                    ]);
                });
            } catch (QueryException $e) {
                if (! $clientMessageId || ! $this->isUniqueConstraintViolation($e)) {
                    throw $e;
                }

                $message = ChatMessage::query()
                    ->where('sender_id', $user->id)
                    ->where('client_message_id', $clientMessageId)
                    ->firstOrFail();
                $idempotent = true;
            }
        }

        $message->loadMissing(['sender', 'repliedMessage.sender']);

        $mode = $this->chatWriteMode();
        if ($mode !== 'degrade_no_broadcast' && $message->event_status !== 'sent') {
            try {
                broadcast(new ChatMessageSent($message))->toOthers();
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

                Log::warning('chat.broadcast_failed', [
                    'group_id' => $group->id,
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
            'message' => $this->formatMessage($message),
            'meta' => [
                'idempotent' => $idempotent,
                'mode' => $mode,
            ],
        ]);
    }

    public function update(Request $request, ChatMessage $message)
    {
        if ($this->chatWriteMode() === 'disable_write') {
            return $this->chatWriteDisabledResponse();
        }

        $this->authorize('update', $message);
        $this->ensureMember($request, (int) $message->group_id);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $message->update([
            'content' => $validated['content'],
            'edited_at' => now(),
        ]);

        return response()->json([
            'message' => [
                'id' => $message->id,
                'group_id' => $message->group_id,
                'sender_id' => $message->sender_id,
                'sender_name' => $request->user()->name,
                'content' => $message->content,
                'reply_to' => $message->repliedMessage ? [
                    'id' => $message->repliedMessage->id,
                    'sender_name' => $message->repliedMessage->sender?->name,
                    'content' => $message->repliedMessage->content,
                ] : null,
                'edited_at' => $message->edited_at?->toIso8601String(),
                'is_forwarded' => (bool) ($message->forwarded_from_message_id || $message->forwarded_from_group_id),
                'forwarded_from_sender_name' => $message->forwarded_from_sender_name,
                'created_at' => $message->created_at?->toIso8601String(),
            ],
        ]);
    }

    public function destroy(Request $request, ChatMessage $message)
    {
        if ($this->chatWriteMode() === 'disable_write') {
            return $this->chatWriteDisabledResponse();
        }

        $this->authorize('delete', $message);
        $this->ensureMember($request, (int) $message->group_id);

        $id = $message->id;
        $message->delete();

        return response()->json([
            'ok' => true,
            'deleted_id' => $id,
        ]);
    }

    public function report(Request $request, ChatMessage $message)
    {
        $this->ensureMember($request, (int) $message->group_id);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        ChatMessageReport::query()->updateOrCreate(
            [
                'chat_message_id' => $message->id,
                'reporter_id' => $request->user()->id,
            ],
            [
                'group_id' => $message->group_id,
                'reason' => $data['reason'],
                'status' => ChatMessageReport::STATUS_PENDING,
                'resolved_by' => null,
                'resolved_at' => null,
                'resolution_note' => null,
            ]
        );

        return response()->json([
            'message' => 'Đã gửi báo cáo cho admin.',
        ]);
    }

    public function forward(Request $request, ChatMessage $message)
    {
        if ($this->chatWriteMode() === 'disable_write') {
            return $this->chatWriteDisabledResponse();
        }

        $this->authorize('forward', $message);
        $user = $request->user();
        $this->ensureMember($request, (int) $message->group_id);

        $validated = $request->validate([
            'target_group_id' => ['required', 'integer', 'exists:chat_groups,id'],
            'client_message_id' => ['nullable', 'string', 'max:100'],
            'parent_event_id' => ['nullable', 'uuid'],
        ]);

        $targetGroupId = (int) $validated['target_group_id'];
        $this->ensureMember($request, $targetGroupId);

        $senderName = $message->sender?->name ?? $message->forwarded_from_sender_name ?? 'User';

        $clientMessageId = $validated['client_message_id'] ?? null;
        $idempotent = false;
        $forwarded = null;

        if ($clientMessageId) {
            $forwarded = ChatMessage::query()
                ->where('sender_id', $user->id)
                ->where('client_message_id', $clientMessageId)
                ->first();
            $idempotent = $forwarded !== null;
        }

        if (! $forwarded) {
            try {
                $forwarded = DB::transaction(function () use ($targetGroupId, $user, $message, $senderName, $validated, $clientMessageId) {
                    return ChatMessage::create([
                        'group_id' => $targetGroupId,
                        'sender_id' => $user->id,
                        'message_uuid' => (string) Str::uuid(),
                        'content' => $message->content,
                        'client_message_id' => $clientMessageId,
                        'event_id' => (string) Str::uuid(),
                        'parent_event_id' => $validated['parent_event_id'] ?? null,
                        'event_status' => 'pending',
                        'next_retry_at' => now(),
                        'forwarded_from_message_id' => $message->id,
                        'forwarded_from_group_id' => (int) $message->group_id,
                        'forwarded_from_sender_name' => $senderName,
                    ]);
                });
            } catch (QueryException $e) {
                if (! $clientMessageId || ! $this->isUniqueConstraintViolation($e)) {
                    throw $e;
                }

                $forwarded = ChatMessage::query()
                    ->where('sender_id', $user->id)
                    ->where('client_message_id', $clientMessageId)
                    ->firstOrFail();
                $idempotent = true;
            }
        }

        $forwarded->loadMissing(['sender', 'repliedMessage.sender']);

        $mode = $this->chatWriteMode();
        if ($mode !== 'degrade_no_broadcast' && $forwarded->event_status !== 'sent') {
            try {
                broadcast(new ChatMessageSent($forwarded))->toOthers();
                $forwarded->forceFill([
                    'event_status' => 'sent',
                    'next_retry_at' => null,
                    'event_last_error' => null,
                ])->save();
            } catch (\Throwable $e) {
                $nextCount = ((int) $forwarded->event_retry_count) + 1;
                $isFinal = $nextCount >= 3;
                $forwarded->forceFill([
                    'event_status' => $isFinal ? 'failed' : 'pending',
                    'event_retry_count' => $nextCount,
                    'next_retry_at' => $isFinal ? null : now()->addSeconds(2 ** $nextCount),
                    'event_last_error' => mb_substr($e->getMessage(), 0, 500),
                ])->save();

                Log::warning('chat.broadcast_failed', [
                    'group_id' => $targetGroupId,
                    'message_id' => $forwarded->id,
                    'message_uuid' => $forwarded->message_uuid,
                    'event_id' => $forwarded->event_id,
                    'parent_event_id' => $forwarded->parent_event_id,
                    'error' => $e->getMessage(),
                ]);
            }
        } elseif ($mode === 'degrade_no_broadcast') {
            $forwarded->forceFill([
                'event_status' => 'failed',
                'next_retry_at' => null,
                'event_last_error' => 'broadcast_skipped_degrade_mode',
            ])->save();
        }

        return response()->json([
            'message' => $this->formatMessage($forwarded),
            'meta' => [
                'idempotent' => $idempotent,
                'mode' => $mode,
            ],
        ]);
    }
}
