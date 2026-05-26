<?php

namespace Tests\Feature;

use App\Models\AdminPermission;
use App\Models\AdminRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminRbacTest extends TestCase
{
    use RefreshDatabase;

    public function test_content_editor_gets_403_on_users_index(): void
    {
        $this->seed(\Database\Seeders\AdminRbacSeeder::class);

        $user = User::factory()->create(['role' => 'admin']);
        $role = AdminRole::query()->where('slug', 'content_editor')->firstOrFail();
        $user->adminRoles()->sync([$role->id]);

        $this->actingAs($user)->get(route('admin.users.index'))->assertForbidden();
    }

    public function test_content_editor_can_access_alphabets_index(): void
    {
        $this->seed(\Database\Seeders\AdminRbacSeeder::class);

        $user = User::factory()->create(['role' => 'admin']);
        $role = AdminRole::query()->where('slug', 'content_editor')->firstOrFail();
        $user->adminRoles()->sync([$role->id]);

        $this->actingAs($user)->get(route('admin.alphabets.index'))->assertOk();
    }

    public function test_super_admin_can_access_users_index(): void
    {
        $this->seed(\Database\Seeders\AdminRbacSeeder::class);

        $user = User::factory()->create(['role' => 'admin']);
        $role = AdminRole::query()->where('slug', 'super_admin')->firstOrFail();
        $user->adminRoles()->sync([$role->id]);

        $this->actingAs($user)->get(route('admin.users.index'))->assertOk();
    }

    public function test_super_admin_can_create_admin_role_with_permissions(): void
    {
        $this->seed(\Database\Seeders\AdminRbacSeeder::class);

        $user = User::factory()->create(['role' => 'admin']);
        $role = AdminRole::query()->where('slug', 'super_admin')->firstOrFail();
        $user->adminRoles()->sync([$role->id]);
        $permission = AdminPermission::query()->where('slug', 'dashboard.view')->firstOrFail();

        $this->actingAs($user)
            ->post(route('admin.admin-roles.store'), [
                'name' => 'Kiểm thử quyền',
                'slug' => 'qa_admin',
                'permission_ids' => [$permission->id],
            ])
            ->assertRedirect(route('admin.admin-roles.index'));

        $this->assertDatabaseHas('admin_roles', [
            'slug' => 'qa_admin',
            'name' => 'Kiểm thử quyền',
        ]);
        $this->assertDatabaseHas('admin_audit_logs', [
            'action' => 'admin_role_created',
        ]);
    }

    public function test_all_admin_routes_are_registered_in_permission_map(): void
    {
        $routeNames = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn ($name) => is_string($name) && str_starts_with($name, 'admin.'))
            ->unique()
            ->values();

        $mappedNames = array_keys(config('admin_route_permissions.routes', []));
        $missing = $routeNames->diff($mappedNames)->values()->all();

        $this->assertSame(
            [],
            $missing,
            'Thiếu permission map cho route admin: '.implode(', ', $missing)
        );
    }

    public function test_super_admin_role_cannot_be_weakened_from_role_editor(): void
    {
        $this->seed(\Database\Seeders\AdminRbacSeeder::class);

        $user = User::factory()->create(['role' => 'admin']);
        $superRole = AdminRole::query()->where('slug', 'super_admin')->firstOrFail();
        $user->adminRoles()->sync([$superRole->id]);

        $permission = AdminPermission::query()->where('slug', 'dashboard.view')->firstOrFail();
        $originalName = $superRole->name;
        $permissionCount = AdminPermission::query()->count();

        $this->actingAs($user)
            ->put(route('admin.admin-roles.update', $superRole), [
                'name' => 'Tên bị đổi nhầm',
                'slug' => 'not_super_anymore',
                'permission_ids' => [$permission->id],
            ])
            ->assertRedirect(route('admin.admin-roles.edit', $superRole));

        $fresh = $superRole->fresh('permissions');
        $this->assertSame('super_admin', $fresh->slug);
        $this->assertSame($originalName, $fresh->name);
        $this->assertCount($permissionCount, $fresh->permissions);
    }

    public function test_last_super_admin_cannot_be_demoted_unassigned_locked_or_deleted(): void
    {
        $this->seed(\Database\Seeders\AdminRbacSeeder::class);

        $actor = User::factory()->create(['role' => 'admin']);
        $target = User::factory()->create(['role' => 'admin']);
        $superRole = AdminRole::query()->where('slug', 'super_admin')->firstOrFail();
        $contentRole = AdminRole::query()->where('slug', 'content_editor')->firstOrFail();
        $target->adminRoles()->sync([$superRole->id]);

        $this->actingAs($actor)
            ->from(route('admin.users.edit', $target))
            ->put(route('admin.users.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'role' => 'user',
            ])
            ->assertRedirect(route('admin.users.edit', $target))
            ->assertSessionHasErrors('role');

        $this->assertSame('admin', $target->fresh()->role);

        $this->actingAs($actor)
            ->from(route('admin.users.edit', $target))
            ->put(route('admin.users.admin-roles.update', $target), [
                'admin_role_ids' => [$contentRole->id],
            ])
            ->assertRedirect(route('admin.users.edit', $target))
            ->assertSessionHasErrors('admin_role_ids');

        $this->assertTrue($target->fresh()->adminRoles()->where('slug', 'super_admin')->exists());

        $this->actingAs($actor)
            ->from(route('admin.users.edit', $target))
            ->post(route('admin.users.lock', $target), ['reason' => 'test'])
            ->assertRedirect(route('admin.users.edit', $target))
            ->assertSessionHas('error');

        $this->assertNull($target->fresh()->locked_at);

        $this->actingAs($actor)
            ->from(route('admin.users.edit', $target))
            ->delete(route('admin.users.destroy', $target))
            ->assertRedirect(route('admin.users.edit', $target))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $target->id]);
    }

    public function test_content_editor_cannot_manage_admin_roles(): void
    {
        $this->seed(\Database\Seeders\AdminRbacSeeder::class);

        $user = User::factory()->create(['role' => 'admin']);
        $role = AdminRole::query()->where('slug', 'content_editor')->firstOrFail();
        $user->adminRoles()->sync([$role->id]);

        $this->actingAs($user)->get(route('admin.admin-roles.index'))->assertForbidden();
    }
}
