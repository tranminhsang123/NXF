<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashcardCardState extends Model
{
    protected $table = 'flashcard_card_states';

    protected $fillable = [
        'user_id',
        'minna_section_id',
        'card_index',
        'ease_factor',
        'repetitions',
        'interval_days',
        'next_review_at',
        'last_reviewed_at',
        'last_quality',
        'lapses',
    ];

    protected $casts = [
        'ease_factor' => 'float',
        'interval_days' => 'float',
        'next_review_at' => 'datetime',
        'last_reviewed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function minnaSection(): BelongsTo
    {
        return $this->belongsTo(MinnaSection::class, 'minna_section_id');
    }
}
