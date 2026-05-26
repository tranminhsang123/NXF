<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LearningEvent extends Model
{
    public const LESSON_VIEWED = 'lesson_viewed';
    public const SECTION_VIEWED = 'section_viewed';
    public const LESSON_COMPLETED = 'lesson_completed';
    public const SECTION_COMPLETED = 'section_completed';
    public const QUIZ_SUBMITTED = 'quiz_submitted';
    public const ADVANCED_QUIZ_SUBMITTED = 'advanced_quiz_submitted';
    public const FLASHCARD_DECK_OPENED = 'flashcard_deck_opened';
    public const FLASHCARD_REVIEWED = 'flashcard_reviewed';
    public const FAVORITE_SAVED = 'favorite_saved';
    public const FAVORITE_REMOVED = 'favorite_removed';
    public const DICTIONARY_LOOKUP = 'dictionary_lookup';
    public const AUDIO_PLAYED = 'audio_played';
    public const POPUP_DICTIONARY_OPENED = 'popup_dictionary_opened';
    public const DASHBOARD_CTA_CLICKED = 'dashboard_cta_clicked';

    protected $fillable = [
        'user_id',
        'event_type',
        'subject_type',
        'subject_id',
        'minna_lesson_id',
        'minna_section_id',
        'session_id',
        'ip_hash',
        'user_agent',
        'metadata',
        'occurred_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'occurred_at' => 'datetime',
    ];

    public static function allowedTypes(): array
    {
        return [
            self::LESSON_VIEWED,
            self::SECTION_VIEWED,
            self::LESSON_COMPLETED,
            self::SECTION_COMPLETED,
            self::QUIZ_SUBMITTED,
            self::ADVANCED_QUIZ_SUBMITTED,
            self::FLASHCARD_DECK_OPENED,
            self::FLASHCARD_REVIEWED,
            self::FAVORITE_SAVED,
            self::FAVORITE_REMOVED,
            self::DICTIONARY_LOOKUP,
            self::AUDIO_PLAYED,
            self::POPUP_DICTIONARY_OPENED,
            self::DASHBOARD_CTA_CLICKED,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function minnaLesson(): BelongsTo
    {
        return $this->belongsTo(MinnaLesson::class);
    }

    public function minnaSection(): BelongsTo
    {
        return $this->belongsTo(MinnaSection::class);
    }
}
