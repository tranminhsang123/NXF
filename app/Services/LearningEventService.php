<?php

namespace App\Services;

use App\Models\LearningEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class LearningEventService
{
    public function record(?User $user, string $eventType, array $payload = [], ?Request $request = null): ?LearningEvent
    {
        if (! in_array($eventType, LearningEvent::allowedTypes(), true)) {
            Log::warning('learning_event.invalid_type', ['event_type' => $eventType]);

            return null;
        }

        try {
            return LearningEvent::query()->create([
                'user_id' => $user?->id,
                'event_type' => $eventType,
                'subject_type' => $payload['subject_type'] ?? null,
                'subject_id' => $payload['subject_id'] ?? null,
                'minna_lesson_id' => $payload['minna_lesson_id'] ?? null,
                'minna_section_id' => $payload['minna_section_id'] ?? null,
                'session_id' => $request?->hasSession() ? $request->session()->getId() : null,
                'ip_hash' => $request?->ip() ? hash('sha256', $request->ip()) : null,
                'user_agent' => $request ? mb_substr((string) $request->userAgent(), 0, 255) : null,
                'metadata' => $this->cleanMetadata($payload['metadata'] ?? []),
                'occurred_at' => now(),
            ]);
        } catch (Throwable $e) {
            Log::warning('learning_event.record_failed', [
                'event_type' => $eventType,
                'user_id' => $user?->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    private function cleanMetadata(array $metadata): array
    {
        return Arr::undot(Arr::dot($metadata));
    }
}
