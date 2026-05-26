<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMinnaSectionProgress extends Model
{
    protected $table = 'user_minna_section_progresses';

    protected $fillable = [
        'user_id',
        'minna_lesson_id',
        'minna_section_id',
        'section_key',
        'status',
        'last_accessed_at',
        'completed_at',
    ];

    protected $casts = [
        'last_accessed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(MinnaLesson::class, 'minna_lesson_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(MinnaSection::class, 'minna_section_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
