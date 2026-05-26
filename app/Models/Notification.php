<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(NotificationRead::class, 'notification_id');
    }

    /**
     * Thông báo gửi cho tất cả admin (user_id = null)
     */
    public static function createForAdmins(string $type, string $title, ?string $message = null, array $data = []): self
    {
        return self::create([
            'user_id' => null,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Thông báo gửi cho một admin cụ thể
     */
    public static function createForUser(User $user, string $type, string $title, ?string $message = null, array $data = []): self
    {
        return self::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
        ]);
    }

    /**
     * Số thông báo chưa đọc cho user (admin)
     */
    public static function unreadCountFor(User $user): int
    {
        $query = self::query()
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->whereDoesntHave('reads', fn ($q) => $q->where('user_id', $user->id));

        return $query->count();
    }

    /**
     * Danh sách thông báo dành cho admin (đã sắp xếp mới nhất trước), kèm trạng thái đã đọc
     */
    public static function forAdmin(User $user, int $perPage = 20)
    {
        return self::query()
            ->where(function ($q) use ($user) {
                $q->whereNull('user_id')->orWhere('user_id', $user->id);
            })
            ->with(['reads' => fn ($q) => $q->where('user_id', $user->id)])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function isReadBy(User $user): bool
    {
        if ($this->relationLoaded('reads')) {
            return $this->reads->where('user_id', $user->id)->isNotEmpty();
        }

        return $this->reads()->where('user_id', $user->id)->exists();
    }
}
