<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinnaSection extends Model
{
    use HasFactory, HasPublishing;

    protected $table = 'minna_sections';

    protected $fillable = [
        'lesson_id',
        'order_index',
        'key',
        'title',
        'content',
        'media_url',
        'publish_status',
        'published_at',
        'archived_at',
    ];

    protected $casts = [
        'content' => 'array',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Quan hệ với lesson
     */
    public function lesson()
    {
        return $this->belongsTo(MinnaLesson::class, 'lesson_id');
    }
}
