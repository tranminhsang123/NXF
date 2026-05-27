<?php

namespace Tests\Feature;

use App\Models\LearningEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PracticalTopicsTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_browse_practical_topics(): void
    {
        $this->get(route('topics.index'))
            ->assertOk()
            ->assertSee('Chủ đề thực tế')
            ->assertSee('Đi du lịch Nhật')
            ->assertSee('Gọi món')
            ->assertSee('Email công việc');
    }

    public function test_user_can_view_topic_with_core_learning_blocks(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('topics.show', ['slug' => 'goi-mon']))
            ->assertOk()
            ->assertSee('Gọi món')
            ->assertSee('Từ vựng')
            ->assertSee('Mẫu câu')
            ->assertSee('Hội thoại')
            ->assertSee('Flashcard')
            ->assertSee('Mini task 5 phút')
            ->assertSee('Quiz');

        $this->assertDatabaseHas('learning_events', [
            'user_id' => $user->id,
            'event_type' => LearningEvent::SECTION_VIEWED,
            'subject_type' => 'practical_topic',
        ]);
    }

    public function test_user_can_submit_topic_quiz(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('topics.quiz', ['slug' => 'goi-mon']), [
                'answers' => [
                    'q1' => 'món gợi ý',
                    'q2' => 'メニューをお願いします。',
                    'q3' => 'thanh toán',
                ],
            ])
            ->assertRedirect(route('topics.show', ['slug' => 'goi-mon']))
            ->assertSessionHas('topic_quiz_result');

        $this->assertDatabaseHas('learning_events', [
            'user_id' => $user->id,
            'event_type' => LearningEvent::QUIZ_SUBMITTED,
            'subject_type' => 'practical_topic_quiz',
        ]);
    }

    public function test_topics_api_returns_topic_pack(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->getJson('/api/learning/topics')
            ->assertOk()
            ->assertJsonPath('topics.0.slug', 'di-du-lich-nhat')
            ->assertJsonPath('topics.0.title', 'Đi du lịch Nhật');

        $this->getJson('/api/learning/topics/goi-mon')
            ->assertOk()
            ->assertJsonPath('topic.slug', 'goi-mon')
            ->assertJsonCount(3, 'topic.quiz');
    }

    public function test_dashboard_promotes_practical_topics(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Chủ đề thực tế')
            ->assertSee('Học tiếng Nhật theo tình huống')
            ->assertSee(route('topics.index'), false);
    }
}
