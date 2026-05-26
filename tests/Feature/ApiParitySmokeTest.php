<?php

namespace Tests\Feature;

use App\Models\MinnaLesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiParitySmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_learning_endpoints_are_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        MinnaLesson::create(['number' => 1, 'title' => 'Bai 01', 'description' => 'Test']);
        Sanctum::actingAs($user);

        $this->getJson('/api/learning/dashboard')->assertOk();
        $this->getJson('/api/minna/lessons')->assertOk();
        $this->getJson('/api/learning/search?q=bai')->assertOk();
    }

    public function test_social_endpoints_are_accessible_for_authenticated_user(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($user);

        $this->getJson('/api/social/groups')->assertOk();
        $this->getJson('/api/social/inbox/conversations')->assertOk();
    }

    public function test_admin_endpoints_require_admin_role(): void
    {
        $normalUser = User::factory()->create(['role' => 'user']);
        Sanctum::actingAs($normalUser);
        $this->getJson('/api/admin/dashboard')->assertForbidden();

        $adminUser = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($adminUser);
        $this->getJson('/api/admin/dashboard')->assertOk();
    }
}
