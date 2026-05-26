<?php

namespace App\Models;

use App\Models\Concerns\HasPublishing;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class N5CourseData extends Model
{
    use HasFactory, HasPublishing;

    protected $table = 'n5_course_data';

    protected $fillable = [
        'section_type',
        'section_key',
        'bai',
        'title',
        'content',
        'order',
        'publish_status',
        'published_at',
        'archived_at',
    ];

    protected $casts = [
        'content' => 'array',
        'published_at' => 'datetime',
        'archived_at' => 'datetime',
    ];
}
