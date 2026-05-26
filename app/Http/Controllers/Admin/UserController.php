<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminRole;
use App\Models\SystemLog;
use App\Models\User;
use App\Services\AdminAuditService;
use App\Services\AdminAudienceSegmentService;
use App\Services\AdminLearningAnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    use PerPageTrait;

    public function index(Request $request, AdminAudienceSegmentService $segmentService)
    {
        $query = User::query();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('locked_status')) {
            if ($request->locked_status === 'locked') {
                $query->whereNotNull('locked_at');
            } elseif ($request->locked_status === 'unlocked') {
                $query->whereNull('locked_at');
            }
        }

        if ($request->filled('learning_segment') && array_key_exists((string) $request->learning_segment, $segmentService->definitions())) {
            $query->whereIn('id', $segmentService->query((string) $request->learning_segment)->select('id'));
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate($this->adminPerPage($request))->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'segmentDefinitions' => $segmentService->definitions(),
        ]);
    }

    public function show(User $user)
    {
        return redirect()->route('admin.users.edit', $user);
    }

    public function edit(User $user, AdminLearningAnalyticsService $analyticsService)
    {
        $allAdminRoles = AdminRole::query()->orderBy('name')->get();
        $user->load('adminRoles');
        $learningProfile = $analyticsService->userLearningProfile($user);

        return view('admin.users.edit', compact('user', 'allAdminRoles', 'learningProfile'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'role' => 'required|in:user,admin',
        ]);

        $wasAdmin = $user->role === 'admin';
        $newRole = (string) $request->input('role');

        if ($wasAdmin && $newRole === 'user' && $this->isLastSuperAdmin($user)) {
            return redirect()->back()
                ->withErrors(['role' => 'Không thể hạ cấp Super Admin cuối cùng của hệ thống.']);
        }

        $before = $user->only(['name', 'email', 'role', 'locked_at', 'locked_reason']);

        $user->update([
            'name' => (string) $request->input('name'),
            'email' => (string) $request->input('email'),
            'role' => $newRole,
        ]);

        if (! $wasAdmin && $newRole === 'admin') {
            $super = AdminRole::query()->where('slug', 'super_admin')->first();
            if ($super) {
                $user->adminRoles()->syncWithoutDetaching([$super->id]);
            }
        }
        if ($wasAdmin && $newRole === 'user') {
            $user->adminRoles()->detach();
        }

        Cache::forget('admin:dashboard:stats');

        app(AdminAuditService::class)->audit(
            $request->user(),
            $user,
            'admin_user_updated',
            'Đã cập nhật tài khoản: '.$user->email,
            $before,
            $user->only(['name', 'email', 'role', 'locked_at', 'locked_reason'])
        );

        return redirect()->route('admin.users.index')
            ->with('success', 'User đã được cập nhật thành công!');
    }

    public function updateAdminRoles(Request $request, User $user)
    {
        abort_unless($user->role === 'admin', 403);

        $validated = $request->validate([
            'admin_role_ids' => 'nullable|array',
            'admin_role_ids.*' => 'integer|exists:admin_roles,id',
        ]);

        $ids = array_values(array_unique($validated['admin_role_ids'] ?? []));

        if (count($ids) < 1) {
            return redirect()->back()
                ->withErrors(['admin_role_ids' => 'Tài khoản admin cần ít nhất một vai trò.']);
        }

        $actor = $request->user();
        $actor->loadMissing('adminRoles');
        $actorMayAssignSuper = $actor->adminRoles->isEmpty()
            || $actor->adminRoles->contains('slug', 'super_admin');

        $superId = AdminRole::query()->where('slug', 'super_admin')->value('id');
        if ($superId && in_array((int) $superId, array_map('intval', $ids), true) && ! $actorMayAssignSuper) {
            abort(403, 'Chỉ Super Admin mới gán vai trò Super Admin.');
        }

        if ($superId && $this->userHasSuperAdminRole($user) && ! in_array((int) $superId, array_map('intval', $ids), true) && $this->isLastSuperAdmin($user)) {
            return redirect()->back()
                ->withErrors(['admin_role_ids' => 'Không thể gỡ vai trò Super Admin khỏi Super Admin cuối cùng của hệ thống.']);
        }

        $before = $user->adminRoles()->pluck('admin_roles.id')->all();
        $user->adminRoles()->sync($ids);
        Cache::forget('admin:dashboard:stats');

        app(AdminAuditService::class)->audit(
            $actor,
            $user,
            'admin_user_roles_updated',
            'Đã cập nhật vai trò admin cho: '.$user->email,
            ['admin_role_ids' => $before],
            ['admin_role_ids' => $ids]
        );

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'Đã cập nhật vai trò RBAC cho tài khoản admin.');
    }

    public function lock(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Bạn không thể khóa chính mình!');
        }

        if ($this->isLastSuperAdmin($user)) {
            return redirect()->back()
                ->with('error', 'Không thể khóa Super Admin cuối cùng của hệ thống.');
        }

        $reason = $request->input('reason', 'Khóa bởi quản trị viên.');
        $before = $user->only(['locked_at', 'locked_reason']);
        $user->update([
            'locked_at' => now(),
            'locked_reason' => $reason,
        ]);
        SystemLog::add($user, 'user_locked', $user->name.' ('.$user->email.') bị khóa bởi admin.', ['source' => 'admin', 'reason' => $reason]);
        Cache::forget('admin:dashboard:stats');

        app(AdminAuditService::class)->audit(
            $request->user(),
            $user,
            'admin_user_locked',
            'Đã khóa tài khoản: '.$user->email,
            $before,
            $user->only(['locked_at', 'locked_reason']),
            ['reason' => $reason]
        );

        return redirect()->back()->with('success', 'Đã khóa tài khoản.');
    }

    public function unlock(Request $request, User $user)
    {
        $before = $user->only(['locked_at', 'locked_reason']);
        $user->update([
            'locked_at' => null,
            'locked_reason' => null,
        ]);
        SystemLog::add($user, 'user_unlocked', $user->name.' ('.$user->email.') được mở khóa bởi admin.', ['source' => 'admin']);
        Cache::forget('admin:dashboard:stats');

        app(AdminAuditService::class)->audit(
            $request->user(),
            $user,
            'admin_user_unlocked',
            'Đã mở khóa tài khoản: '.$user->email,
            $before,
            $user->only(['locked_at', 'locked_reason'])
        );

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'Đã mở khóa tài khoản.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Bạn không thể xóa chính mình!');
        }

        if ($this->isLastSuperAdmin($user)) {
            return redirect()->back()
                ->with('error', 'Không thể xóa Super Admin cuối cùng của hệ thống.');
        }

        $before = $user->only(['id', 'name', 'email', 'role', 'locked_at', 'locked_reason']);

        app(AdminAuditService::class)->audit(
            $request->user(),
            $user,
            'admin_user_deleted',
            'Đã xóa tài khoản: '.$user->email,
            $before
        );

        $user->delete();
        Cache::forget('admin:dashboard:stats');

        return redirect()->route('admin.users.index')
            ->with('success', 'User đã được xóa thành công!');
    }

    private function userHasSuperAdminRole(User $user): bool
    {
        return $user->role === 'admin'
            && $user->adminRoles()->where('slug', 'super_admin')->exists();
    }

    private function isLastSuperAdmin(User $user): bool
    {
        if (! $this->userHasSuperAdminRole($user)) {
            return false;
        }

        $superId = AdminRole::query()->where('slug', 'super_admin')->value('id');
        if (! $superId) {
            return false;
        }

        return ! User::query()
            ->where('role', 'admin')
            ->whereKeyNot($user->getKey())
            ->whereHas('adminRoles', fn ($query) => $query->where('admin_roles.id', $superId))
            ->exists();
    }
}
