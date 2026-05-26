<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ContentVersion extends Model
{
    protected $fillable = [
        'versionable_type',
        'versionable_id',
        'actor_id',
        'action',
        'snapshot',
        'changes',
        'notes',
    ];

    protected $casts = [
        'snapshot' => 'array',
        'changes' => 'array',
    ];

    public function versionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }
}
