<?php

namespace Tests\Feature;

use App\Models\MinnaLesson;
use App\Models\MinnaQuizAttempt;
use App\Models\MinnaSection;
use App\Models\User;
use App\Models\UserMinnaSectionProgress;
use App\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningPathWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_roadmap(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        MinnaLesson::query()->create(['number' => 1, 'title' => 'Bai 01']);

        $this->actingAs($user)
            ->get(route('minna.roadmap'))
            ->assertOk()
            ->assertSee('Lộ trình Minna');
    }

    public function test_user_can_complete_a_lesson_section(): void
    {
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
            ->assertRedirect(route('minna.show', ['number' => $lesson->number]));

        $this->assertDatabaseHas('user_minna_section_progresses', [
            'user_id' => $user->id,
            'minna_section_id' => $section->id,
            'status' => UserProgress::STATUS_COMPLETED,
        ]);
    }

    public function test_quiz_attempt_is_recorded_and_can_pass_lesson(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $lesson = MinnaLesson::query()->create(['number' => 1, 'title' => 'Bai 01']);
        MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Tu vung',
            'content' => [
                'vocab' => [
                    ['tu_vung' => 'a', 'nghia' => 'A'],
                    ['tu_vung' => 'b', 'nghia' => 'B'],
                    ['tu_vung' => 'c', 'nghia' => 'C'],
                    ['tu_vung' => 'd', 'nghia' => 'D'],
                    ['tu_vung' => 'e', 'nghia' => 'E'],
                ],
            ],
        ]);

        $this->actingAs($user)
            ->post(route('minna.quiz.submit', ['number' => $lesson->number]), [
                'answers' => ['A', 'B', 'C', 'D', 'E'],
            ])
            ->assertRedirect(route('minna.show', ['number' => $lesson->number]));

        $this->assertDatabaseHas('minna_quiz_attempts', [
            'user_id' => $user->id,
            'minna_lesson_id' => $lesson->id,
            'score' => 5,
            'total' => 5,
            'passed' => true,
        ]);

        $this->assertDatabaseHas('user_progresses', [
            'user_id' => $user->id,
            'lesson_id' => $lesson->id,
            'status' => UserProgress::STATUS_COMPLETED,
        ]);
    }

    public function test_user_can_view_activity_timeline(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $lesson = MinnaLesson::query()->create(['number' => 1, 'title' => 'Bai 01']);
        MinnaQuizAttempt::query()->create([
            'user_id' => $user->id,
            'minna_lesson_id' => $lesson->id,
            'score' => 4,
            'total' => 5,
            'percent' => 80,
            'passed' => true,
            'completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('user.activity'))
            ->assertOk()
            ->assertSee('Lịch sử học tập')
            ->assertSee('Quiz đạt');
    }
}
