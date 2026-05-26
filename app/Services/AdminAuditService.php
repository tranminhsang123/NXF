<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use App\Models\ContentVersion;
use App\Models\User;
use App\Support\AdminContentRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Request;

class AdminAuditService
{
    public function contentChanged(Model $model, string $action, array $before = [], ?User $actor = null, ?array $changes = null): void
    {
        $type = AdminContentRegistry::typeFor($model);
        if (! $type) {
            return;
        }

        $actor = $actor ?: auth()->user();
        $after = AdminContentRegistry::snapshot($model);
        $changes = $changes ?? $this->diff($before, $after);

        ContentVersion::query()->create([
            'versionable_type' => $model::class,
            'versionable_id' => $model->getKey(),
            'actor_id' => $actor?->id,
            'action' => $action,
            'snapshot' => $after,
            'changes' => $changes ?: null,
        ]);

        $this->audit(
            $actor,
            $model,
            $action,
            AdminContentRegistry::labelFor($model).' '.$action.': '.AdminContentRegistry::titleFor($model),
            $before ?: null,
            $after,
            ['content_type' => $type]
        );
    }

    public function audit(
        ?User $actor,
        ?Model $model,
        string $action,
        string $summary,
        ?array $before = null,
        ?array $after = null,
        array $metadata = []
    ): void {
        AdminAuditLog::query()->create([
            'actor_id' => $actor?->id,
            'auditable_type' => $model ? $model::class : null,
            'auditable_id' => $model?->getKey(),
            'action' => $action,
            'summary' => mb_substr($summary, 0, 500),
            'before' => $before,
            'after' => $after,
            'metadata' => $metadata ?: null,
            'ip_address' => Request::ip(),
            'user_agent' => mb_substr((string) Request::userAgent(), 0, 500),
            'created_at' => now(),
        ]);
    }

    public function diff(array $before, array $after): array
    {
        $diff = [];

        foreach ($after as $key => $value) {
            $old = $before[$key] ?? null;
            if ($old != $value) {
                $diff[$key] = [
                    'before' => $old,
                    'after' => $value,
                ];
            }
        }

        return $diff;
    }
}
