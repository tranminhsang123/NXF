<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProgress extends Model
{
    use HasFactory;

    /**
     * Bảng được sử dụng cho model.
     *
     * Mặc định migration mới tạo bảng `user_progresses`.
     */
    protected $table = 'user_progresses';

    // Các loại bài học
    public const TYPE_MINNA = 'minna';

    // Trạng thái tiến độ
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';

    protected $fillable = [
        'user_id',
        'lesson_type',
        'lesson_id',
        'status',
        'last_accessed_at',
        'completed_at',
    ];

    protected $casts = [
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(MinnaLesson::class, 'lesson_id');
    }
}

