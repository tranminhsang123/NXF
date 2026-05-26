<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'created_by',
    ];

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_group_members', 'group_id', 'user_id')
            ->withTimestamps();
    }

    public function groupMembers(): HasMany
    {
        return $this->hasMany(ChatGroupMember::class, 'group_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'group_id');
    }
}

