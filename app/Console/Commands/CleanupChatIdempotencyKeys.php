<?php

namespace App\Console\Commands;

use App\Models\ChatMessage;
use App\Models\DirectMessage;
use Illuminate\Console\Command;

class CleanupChatIdempotencyKeys extends Command
{
    protected $signature = 'chat:cleanup-idempotency {--hours= : Override TTL hours} {--limit=5000 : Max rows per table}';

    protected $description = 'Clear old client_message_id keys after idempotency window.';

    public function handle(): int
    {
        $ttlHours = (int) ($this->option('hours') ?? config('chat.idempotency_ttl_hours', 72));
        $limit = max(1, (int) $this->option('limit'));
        $cutoff = now()->subHours(max(1, $ttlHours));

        $chatUpdated = ChatMessage::query()
            ->whereNotNull('client_message_id')
            ->where('created_at', '<', $cutoff)
            ->orderBy('id')
            ->limit($limit)
            ->update(['client_message_id' => null]);

        $directUpdated = DirectMessage::query()
            ->whereNotNull('client_message_id')
            ->where('created_at', '<', $cutoff)
            ->orderBy('id')
            ->limit($limit)
            ->update(['client_message_id' => null]);

        $this->info("Cleared idempotency keys. chat={$chatUpdated}, direct={$directUpdated}, ttl_hours={$ttlHours}");

        return self::SUCCESS;
    }
}
