<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'locked_at',
        'locked_reason',
        'onboarding_level',
        'jlpt_goal',
        'daily_study_minutes',
        'learning_reasons',
        'placement_test_score',
        'placement_test_level',
        'placement_answers',
        'email_reminders_enabled',
        'onboarding_completed_at',
        'quick_win_started_at',
        'quick_win_completed_at',
        'last_study_reminder_sent_at',
        'daily_goal_minna_lessons',
        'daily_goal_flashcards',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'locked_at' => 'datetime',
        'password' => 'hashed',
        'last_study_date' => 'date',
        'learning_reasons' => 'array',
        'placement_answers' => 'array',
        'email_reminders_enabled' => 'boolean',
        'onboarding_completed_at' => 'datetime',
        'quick_win_started_at' => 'datetime',
        'quick_win_completed_at' => 'datetime',
        'last_study_reminder_sent_at' => 'datetime',
    ];

    /**
     * Kiểm tra và áp dụng tự mở khóa sau X giờ (soft-ban). Trả về true nếu vẫn đang khóa.
     */
    public function refreshLockState(): bool
    {
        if ($this->locked_at === null) {
            return false;
        }
        $hours = (int) \App\Models\SecuritySetting::get('devtools_auto_unlock_hours', '0');
        if ($hours <= 0) {
            return true;
        }
        if ($this->locked_at->addHours($hours)->isPast()) {
            $this->update(['locked_at' => null, 'locked_reason' => null]);
            $this->refresh();
            SystemLog::add($this, 'user_auto_unlocked', 'Tài khoản được tự động mở khóa sau '.$hours.' giờ.', ['hours' => $hours]);

            return false;
        }

        return true;
    }

    public function isLocked(): bool
    {
        return $this->refreshLockState();
    }

    public function devtoolsViolations(): HasMany
    {
        return $this->hasMany(DevtoolsViolation::class);
    }

    public function progresses(): HasMany
    {
        return $this->hasMany(UserProgress::class);
    }

    public function learningEvents(): HasMany
    {
        return $this->hasMany(LearningEvent::class);
    }

    public function flashcardCardStates(): HasMany
    {
        return $this->hasMany(FlashcardCardState::class);
    }

    public function favoriteItems(): HasMany
    {
        return $this->hasMany(FavoriteItem::class);
    }

    public function minnaQuizAttempts(): HasMany
    {
        return $this->hasMany(MinnaQuizAttempt::class);
    }

    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'badge_user')
            ->withPivot('earned_at')
            ->withTimestamps()
            ->orderBy('badges.sort_order');
    }

    /** Cấp độ hiển thị từ tổng XP (bậc thang đơn giản). */
    public function gamificationLevel(): int
    {
        $xp = (int) ($this->xp_total ?? 0);
        $thresholds = [0, 40, 100, 200, 350, 550, 800, 1200, 1700, 2400, 3300];
        $level = 1;
        foreach ($thresholds as $t) {
            if ($xp >= $t) {
                $level++;
            }
        }

        return min($level, 99);
    }

    public function adminRoles(): BelongsToMany
    {
        return $this->belongsToMany(AdminRole::class, 'admin_role_user', 'user_id', 'admin_role_id');
    }

    /**
     * Admin chưa gán role nào: giữ hành vi cũ (full quyền) để tương thích migrate.
     * Có ít nhất một role: chỉ được làm việc theo permission (super_admin = full).
     */
    public function adminBypassesRoutePermissionMap(): bool
    {
        if ($this->role !== 'admin') {
            return false;
        }
        $this->loadMissing('adminRoles');

        return $this->adminRoles->isEmpty() || $this->adminRoles->contains('slug', 'super_admin');
    }

    public function hasAdminPermission(string $permission): bool
    {
        if ($this->role !== 'admin') {
            return false;
        }

        $this->loadMissing('adminRoles.permissions');

        if ($this->adminRoles->isEmpty()) {
            return true;
        }

        foreach ($this->adminRoles as $role) {
            if ($role->slug === 'super_admin') {
                return true;
            }
            if ($role->permissions->contains('slug', $permission)) {
                return true;
            }
        }

        return false;
    }
}
