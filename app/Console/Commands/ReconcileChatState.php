<?php

namespace App\Console\Commands;

use App\Models\DirectConversation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReconcileChatState extends Command
{
    protected $signature = 'chat:reconcile-state {--days=14 : Active window in days} {--limit=500 : Max conversations per run}';

    protected $description = 'Recalculate lightweight chat state for active direct conversations.';

    public function handle(): int
    {
        $days = max(1, (int) $this->option('days'));
        $limit = max(1, (int) $this->option('limit'));
        $cutoff = now()->subDays($days);

        $updated = 0;

        $conversations = DirectConversation::query()
            ->where(function ($q) use ($cutoff) {
                $q->where('last_message_at', '>=', $cutoff)
                    ->orWhereHas('messages', fn ($mq) => $mq->where('created_at', '>=', $cutoff));
            })
            ->orderByDesc('last_message_at')
            ->limit($limit)
            ->get();

        foreach ($conversations as $conversation) {
            $latestMessageAt = DB::table('direct_messages')
                ->where('conversation_id', $conversation->id)
                ->max('created_at');

            if ($latestMessageAt && (string) $conversation->last_message_at !== (string) $latestMessageAt) {
                $conversation->forceFill(['last_message_at' => $latestMessageAt])->save();
                $updated++;
            }

            // Lightweight unread reconciliation signal by recomputing authoritative counts.
            $userUnread = DB::table('direct_messages')
                ->where('conversation_id', $conversation->id)
                ->where('recipient_id', $conversation->user_id)
                ->whereNull('read_at')
                ->count();
            $adminUnread = DB::table('direct_messages')
                ->where('conversation_id', $conversation->id)
                ->where('recipient_id', $conversation->admin_id)
                ->whereNull('read_at')
                ->count();

            Log::info('chat.reconcile.unread', [
                'conversation_id' => $conversation->id,
                'user_id' => $conversation->user_id,
                'admin_id' => $conversation->admin_id,
                'user_unread' => $userUnread,
                'admin_unread' => $adminUnread,
            ]);
        }

        $this->info("Reconciled {$conversations->count()} conversations, updated_last_message_at={$updated}");

        return self::SUCCESS;
    }
}
