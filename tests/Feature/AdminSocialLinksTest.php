<?php

namespace Tests\Feature;

use App\Models\SocialLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSocialLinksTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_manage_social_links_from_settings(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.logo-settings.index'))
            ->assertOk()
            ->assertSee('Mạng xã hội')
            ->assertSee('CRUD link social');

        $this->actingAs($admin)
            ->post(route('admin.social-links.store'), [
                'platform' => 'tiktok',
                'label' => 'TikTok Yamato',
                'url' => 'https://www.tiktok.com/@yamato',
                'sort_order' => 55,
                'is_active' => '1',
            ])
            ->assertRedirect(route('admin.logo-settings.index'));

        $socialLink = SocialLink::query()->where('platform', 'tiktok')->firstOrFail();
        $this->assertTrue($socialLink->is_active);

        $this->actingAs($admin)
            ->put(route('admin.social-links.update', $socialLink), [
                'platform' => 'instagram',
                'label' => 'Instagram Yamato',
                'url' => 'https://www.instagram.com/yamato',
                'sort_order' => 15,
            ])
            ->assertRedirect(route('admin.logo-settings.index'));

        $this->assertDatabaseHas('social_links', [
            'id' => $socialLink->id,
            'platform' => 'instagram',
            'label' => 'Instagram Yamato',
            'is_active' => false,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.social-links.destroy', $socialLink))
            ->assertRedirect(route('admin.logo-settings.index'));

        $this->assertDatabaseMissing('social_links', [
            'id' => $socialLink->id,
        ]);
    }
}
