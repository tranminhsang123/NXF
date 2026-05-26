<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminRoleController extends Controller
{
    public function index()
    {
        $roles = AdminRole::query()
            ->withCount(['permissions', 'users'])
            ->orderBy('name')
            ->paginate(20);

        return view('admin.admin-roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.admin-roles.create', [
            'role' => new AdminRole(),
            'permissionGroups' => $this->permissionGroups(),
            'selectedPermissionIds' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedRoleData($request);

        $role = AdminRole::query()->create([
            'name' => $data['name'],
            'slug' => $data['slug'],
        ]);
        $role->permissions()->sync($data['permission_ids']);

        app(AdminAuditService::class)->audit(
            $request->user(),
            $role,
            'admin_role_created',
            'Đã tạo vai trò admin: '.$role->name,
            null,
            ['name' => $role->name, 'slug' => $role->slug],
            ['permission_ids' => $data['permission_ids']]
        );

        return redirect()->route('admin.admin-roles.index')->with('success', 'Đã tạo vai trò admin.');
    }

    public function edit(AdminRole $adminRole)
    {
        $adminRole->load('permissions');

        return view('admin.admin-roles.edit', [
            'role' => $adminRole,
            'permissionGroups' => $this->permissionGroups(),
            'selectedPermissionIds' => $adminRole->permissions->pluck('id')->all(),
        ]);
    }

    public function update(Request $request, AdminRole $adminRole)
    {
        $before = [
            'name' => $adminRole->name,
            'slug' => $adminRole->slug,
            'permission_ids' => $adminRole->permissions()->pluck('admin_permissions.id')->all(),
        ];

        $data = $this->validatedRoleData($request, $adminRole);

        $isSuperRole = $adminRole->slug === 'super_admin';

        $adminRole->update([
            'name' => $isSuperRole ? $adminRole->name : $data['name'],
            'slug' => $isSuperRole ? 'super_admin' : $data['slug'],
        ]);

        $permissionIds = $isSuperRole
            ? AdminPermission::query()->pluck('id')->all()
            : $data['permission_ids'];
        $adminRole->permissions()->sync($permissionIds);

        app(AdminAuditService::class)->audit(
            $request->user(),
            $adminRole,
            'admin_role_updated',
            'Đã cập nhật vai trò admin: '.$adminRole->name,
            $before,
            [
                'name' => $adminRole->name,
                'slug' => $adminRole->slug,
                'permission_ids' => $permissionIds,
            ]
        );

        return redirect()->route('admin.admin-roles.edit', $adminRole)->with('success', 'Đã cập nhật vai trò admin.');
    }

    public function destroy(Request $request, AdminRole $adminRole)
    {
        abort_if($adminRole->slug === 'super_admin', 422, 'Không thể xoá vai trò Super Admin.');
        abort_if($adminRole->users()->exists(), 422, 'Không thể xoá vai trò đang được gán cho admin.');

        $before = $adminRole->only(['id', 'name', 'slug']);
        $adminRole->permissions()->detach();
        $adminRole->delete();

        app(AdminAuditService::class)->audit(
            $request->user(),
            null,
            'admin_role_deleted',
            'Đã xoá vai trò admin: '.$before['name'],
            $before
        );

        return redirect()->route('admin.admin-roles.index')->with('success', 'Đã xoá vai trò admin.');
    }

    private function validatedRoleData(Request $request, ?AdminRole $role = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9_]+$/',
                Rule::unique('admin_roles', 'slug')->ignore($role?->id),
            ],
            'permission_ids' => ['required', 'array', 'min:1'],
            'permission_ids.*' => ['integer', 'exists:admin_permissions,id'],
        ]);

        $data['permission_ids'] = array_values(array_unique(array_map('intval', $data['permission_ids'])));

        return $data;
    }

    private function permissionGroups()
    {
        return AdminPermission::query()
            ->orderBy('slug')
            ->get()
            ->groupBy(fn (AdminPermission $permission) => str($permission->slug)->before('.')->toString());
    }
}
