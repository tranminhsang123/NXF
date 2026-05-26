<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AdminRole extends Model
{
    protected $fillable = ['slug', 'name'];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_permission_role', 'admin_role_id', 'admin_permission_id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'admin_role_user', 'admin_role_id', 'user_id');
    }
}
