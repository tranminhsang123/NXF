<?php

namespace App\Models\Concerns;

use App\Support\PublishStatus;
use Illuminate\Database\Eloquent\Builder;

trait HasPublishing
{
    public const STATUS_DRAFT = PublishStatus::DRAFT;
    public const STATUS_PUBLISHED = PublishStatus::PUBLISHED;
    public const STATUS_ARCHIVED = PublishStatus::ARCHIVED;

    public static function publishStatuses(): array
    {
        return PublishStatus::labels();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where($this->getTable().'.publish_status', self::STATUS_PUBLISHED);
    }

    public function isPublished(): bool
    {
        return ($this->publish_status ?? self::STATUS_PUBLISHED) === self::STATUS_PUBLISHED;
    }

    public function publishStatusLabel(): string
    {
        return self::publishStatuses()[$this->publish_status ?? self::STATUS_PUBLISHED] ?? 'Không rõ';
    }
}
