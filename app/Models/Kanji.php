<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kanji extends Model
{
    use HasFactory, HasPublishing;

    protected $fillable = [
        'character',
        'meaning',
        'on_reading',
        'kun_reading',
        'level',
        'stroke_count',
        'radical',
        'examples',
        'publish_status',
        'published_at',
        'archived_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    // Scope để lọc theo cấp độ
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }
}
