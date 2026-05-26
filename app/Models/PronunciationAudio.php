<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PronunciationAudio extends Model
{
    protected $table = 'pronunciation_audios';

    protected $fillable = [
        'text_hash',
        'text',
        'language',
        'source',
        'audio_url',
        'metadata',
        'usage_count',
        'last_used_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'last_used_at' => 'datetime',
    ];
}
