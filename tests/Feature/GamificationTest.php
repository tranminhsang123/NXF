<?php

namespace Tests\Feature;

use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\User;
use Database\Seeders\BadgeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GamificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_flashcard_review_awards_xp_and_updates_streak(): void
    {
        $this->seed(BadgeSeeder::class);

        $user = User::factory()->create(['role' => 'user']);
        $lesson = MinnaLesson::query()->create(['number' => 1, 'title' => 'Bai 01']);
        $section = MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Tu vung',
            'content' => [
                'vocab' => [
                    ['tu_vung' => 'x', 'nghia' => 'X'],
                ],
            ],
        ]);

        $this->actingAs($user)
            ->postJson(route('flashcard.review'), [
                'minna_section_id' => $section->id,
                'card_index' => 0,
                'quality' => 4,
            ])
            ->assertOk()
            ->assertJsonPath('gamification.xp_gained', 4)
            ->assertJsonPath('gamification.xp_total', 4);

        $user->refresh();
        $this->assertSame(1, $user->current_streak);
        $this->assertSame(1, $user->longest_streak);
        $this->assertTrue($user->badges()->where('slug', 'first_review')->exists());
    }

    public function test_minna_section_complete_awards_xp(): void
    {
        $this->seed(BadgeSeeder::class);

        $user = User::factory()->create(['role' => 'user']);
        $lesson = MinnaLesson::query()->create(['number' => 1, 'title' => 'Bai 01']);
        $section = MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Tu vung',
            'content' => ['vocab' => []],
        ]);

        $this->actingAs($user)
            ->post(route('minna.section.complete', ['number' => $lesson->number, 'section' => $section->id]))
            ->assertRedirect();

        $user->refresh();
        $this->assertSame(33, $user->xp_total);
    }
}
