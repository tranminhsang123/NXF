<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MinnaLesson extends Model
{
    use HasFactory, HasPublishing;

    protected $table = 'minna_lessons';

    protected $fillable = [
        'number',
        'title',
        'description',
        'publish_status',
        'published_at',
        'archived_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    /**
     * Quan hệ với sections
     */
    public function sections()
    {
        return $this->hasMany(MinnaSection::class, 'lesson_id')->orderBy('order_index');
    }

    /**
     * Lấy section theo key
     */
    public function getSectionByKey($key)
    {
        return $this->sections()->where('key', $key)->first();
    }
}
