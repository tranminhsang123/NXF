<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alphabet extends Model
{
    use HasFactory, HasPublishing;

    protected $fillable = [
        'character',
        'romaji',
        'type',
        'category',
        'publish_status',
        'published_at',
        'archived_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    // Scope để lọc theo loại
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }
}
