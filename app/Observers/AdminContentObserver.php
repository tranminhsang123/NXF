<?php

namespace App\Observers;

use App\Services\AdminAuditService;
use App\Support\AdminContentRegistry;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class AdminContentObserver
{
    public function created(Model $model): void
    {
        $this->record($model, 'created', []);
    }

    public function updated(Model $model): void
    {
        $this->record($model, 'updated', $model->getOriginal());
    }

    public function deleted(Model $model): void
    {
        $this->record($model, 'deleted', AdminContentRegistry::snapshot($model));
    }

    private function record(Model $model, string $action, array $before): void
    {
        if (! AdminContentRegistry::typeFor($model)) {
            return;
        }

        try {
            app(AdminAuditService::class)->contentChanged($model, $action, $before);
        } catch (Throwable) {
            // Audit must not block content operations.
        }
    }
}
