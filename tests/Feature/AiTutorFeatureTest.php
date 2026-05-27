<?php

namespace Tests\Feature;

use App\Models\LearningEvent;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiTutorFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_lesson_page_shows_ai_tutor_panel_for_logged_in_user(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'onboarding_level' => 'N5',
            'jlpt_goal' => 'N5',
        ]);
        $lesson = $this->createLesson();

        $this->actingAs($user)
            ->get(route('minna.show', ['number' => $lesson->number]))
            ->assertOk()
            ->assertSee('Trợ lý học tiếng Nhật')
            ->assertSee('AI Tutor theo bài '.$lesson->number);
    }

    public function test_ai_tutor_answers_with_lesson_and_user_context(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'onboarding_level' => 'N5',
            'jlpt_goal' => 'N4',
            'daily_study_minutes' => 25,
            'learning_reasons' => ['work'],
        ]);
        $lesson = $this->createLesson();

        $this->actingAs($user)
            ->postJson(route('minna.ai-tutor', ['number' => $lesson->number]), [
                'action' => 'summarize_lesson',
            ])
            ->assertOk()
            ->assertJsonPath('ok', true)
            ->assertJsonPath('answer.context_summary.lesson', 'Bài '.$lesson->number.' - '.$lesson->title)
            ->assertJsonPath('answer.context_summary.level', 'N5')
            ->assertJsonPath('answer.context_summary.goal', 'N4');

        $this->assertDatabaseHas('learning_events', [
            'user_id' => $user->id,
            'event_type' => LearningEvent::AI_TUTOR_USED,
            'minna_lesson_id' => $lesson->id,
        ]);
    }

    public function test_ai_tutor_can_create_controlled_mini_quiz_and_check_translation(): void
    {
        $user = User::factory()->create(['role' => 'user', 'onboarding_level' => 'N5']);
        $lesson = $this->createLesson();

        $this->actingAs($user)
            ->postJson(route('minna.ai-tutor', ['number' => $lesson->number]), [
                'action' => 'mini_quiz',
            ])
            ->assertOk()
            ->assertJsonCount(5, 'answer.quiz');

        $this->actingAs($user)
            ->postJson(route('minna.ai-tutor', ['number' => $lesson->number]), [
                'action' => 'check_translation',
                'prompt' => 'Tôi muốn dịch từ sách và nước sang tiếng Nhật',
            ])
            ->assertOk()
            ->assertJsonPath('answer.action', 'check_translation')
            ->assertJsonFragment(['provider' => 'local']);
    }

    private function createLesson(): MinnaLesson
    {
        $lesson = MinnaLesson::query()->create([
            'number' => 31,
            'title' => 'Bài AI Tutor',
            'description' => 'Bài dùng để kiểm thử trợ lý học.',
        ]);

        MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Từ vựng',
            'content' => [
                'vocab' => [
                    ['tu_vung' => 'ほん', 'nghia' => 'sách'],
                    ['tu_vung' => 'みず', 'nghia' => 'nước'],
                    ['tu_vung' => 'がっこう', 'nghia' => 'trường học'],
                    ['tu_vung' => 'せんせい', 'nghia' => 'giáo viên'],
                    ['tu_vung' => 'ともだち', 'nghia' => 'bạn bè'],
                ],
            ],
        ]);

        MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 2,
            'key' => 'ngu-phap',
            'title' => 'Ngữ pháp',
            'content' => [
                [
                    'title' => 'N1 は N2 です',
                    'pattern' => 'N1 は N2 です',
                    'meaning' => 'Dùng để giới thiệu hoặc khẳng định.',
                ],
            ],
        ]);

        return $lesson->fresh('sections');
    }
}
