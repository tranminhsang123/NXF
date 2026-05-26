<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentErrorReport extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_DISMISSED = 'dismissed';

    public const CATEGORY_TYPO = 'typo';
    public const CATEGORY_TRANSLATION = 'translation';
    public const CATEGORY_AUDIO = 'audio';
    public const CATEGORY_QUIZ = 'quiz';
    public const CATEGORY_CONTENT = 'content';
    public const CATEGORY_OTHER = 'other';

    protected $fillable = [
        'user_id',
        'assigned_to',
        'resolved_by',
        'content_type',
        'content_id',
        'content_title',
        'category',
        'status',
        'page_url',
        'selected_text',
        'description',
        'browser_context',
        'resolution_note',
        'resolved_at',
    ];

    protected $casts = [
        'browser_context' => 'array',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public static function statusLabels(): array
    {
        return [
            self::STATUS_PENDING => 'Chờ xử lý',
            self::STATUS_IN_PROGRESS => 'Đang xử lý',
            self::STATUS_RESOLVED => 'Đã xử lý',
            self::STATUS_DISMISSED => 'Bỏ qua',
        ];
    }

    public static function categoryLabels(): array
    {
        return [
            self::CATEGORY_TYPO => 'Sai chính tả',
            self::CATEGORY_TRANSLATION => 'Sai nghĩa/dịch',
            self::CATEGORY_AUDIO => 'Lỗi audio/phát âm',
            self::CATEGORY_QUIZ => 'Lỗi quiz',
            self::CATEGORY_CONTENT => 'Sai nội dung bài học',
            self::CATEGORY_OTHER => 'Khác',
        ];
    }

    public function statusLabel(): string
    {
        return self::statusLabels()[$this->status] ?? $this->status;
    }

    public function categoryLabel(): string
    {
        return self::categoryLabels()[$this->category] ?? $this->category;
    }
}
