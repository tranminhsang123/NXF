<?php

namespace App\Console\Commands;

use App\Events\ChatMessageSent;
use App\Events\DirectMessageSent;
use App\Models\ChatMessage;
use App\Models\DirectMessage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryPendingChatEvents extends Command
{
    protected $signature = 'chat:retry-pending-events {--limit=200 : Max rows per table}';

    protected $description = 'Retry broadcasting pending chat and direct message events.';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $chatRetried = 0;
        $directRetried = 0;

        ChatMessage::query()
            ->where('event_status', 'pending')
            ->where(function ($q) {
                $q->whereNull('next_retry_at')->orWhere('next_retry_at', '<=', now());
            })
            ->where('event_retry_count', '<', 3)
            ->orderBy('id')
            ->limit($limit)
            ->each(function (ChatMessage $message) use (&$chatRetried) {
                try {
                    broadcast(new ChatMessageSent($message))->toOthers();
                    $message->forceFill([
                        'event_status' => 'sent',
                        'next_retry_at' => null,
                        'event_last_error' => null,
                    ])->save();
                    $chatRetried++;
                } catch (\Throwable $e) {
                    $nextCount = ((int) $message->event_retry_count) + 1;
                    $isFinal = $nextCount >= 3;
                    $message->forceFill([
                        'event_status' => $isFinal ? 'failed' : 'pending',
                        'event_retry_count' => $nextCount,
                        'next_retry_at' => $isFinal ? null : now()->addSeconds(2 ** $nextCount),
                        'event_last_error' => mb_substr($e->getMessage(), 0, 500),
                    ])->save();

                    Log::warning('chat.pending_event_retry_failed', [
                        'message_id' => $message->id,
                        'message_uuid' => $message->message_uuid,
                        'event_id' => $message->event_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            });

        DirectMessage::query()
            ->where('event_status', 'pending')
            ->where(function ($q) {
                $q->whereNull('next_retry_at')->orWhere('next_retry_at', '<=', now());
            })
            ->where('event_retry_count', '<', 3)
            ->orderBy('id')
            ->limit($limit)
            ->each(function (DirectMessage $message) use (&$directRetried) {
                try {
                    broadcast(new DirectMessageSent($message))->toOthers();
                    $message->forceFill([
                        'event_status' => 'sent',
                        'next_retry_at' => null,
                        'event_last_error' => null,
                    ])->save();
                    $directRetried++;
                } catch (\Throwable $e) {
                    $nextCount = ((int) $message->event_retry_count) + 1;
                    $isFinal = $nextCount >= 3;
                    $message->forceFill([
                        'event_status' => $isFinal ? 'failed' : 'pending',
                        'event_retry_count' => $nextCount,
                        'next_retry_at' => $isFinal ? null : now()->addSeconds(2 ** $nextCount),
                        'event_last_error' => mb_substr($e->getMessage(), 0, 500),
                    ])->save();

                    Log::warning('direct.pending_event_retry_failed', [
                        'message_id' => $message->id,
                        'message_uuid' => $message->message_uuid,
                        'event_id' => $message->event_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            });

        $this->info("Retried events. chat={$chatRetried}, direct={$directRetried}");

        return self::SUCCESS;
    }
}
