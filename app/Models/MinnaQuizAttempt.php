<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MinnaQuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'minna_lesson_id',
        'score',
        'total',
        'percent',
        'passed',
        'answers_snapshot',
        'completed_at',
    ];

    protected $casts = [
        'answers_snapshot' => 'array',
        'passed' => 'bool',
        'completed_at' => 'datetime',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(MinnaLesson::class, 'minna_lesson_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
