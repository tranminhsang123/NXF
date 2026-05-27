<?php

namespace Tests\Feature;

use App\Models\FlashcardCardState;
use App\Models\LearningEvent;
use App\Models\MinnaLesson;
use App\Models\MinnaQuizAttempt;
use App\Models\MinnaSection;
use App\Models\User;
use App\Models\UserProgress;
use App\Support\PublishStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WeeklyGoalTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_dashboard_shows_weekly_goals(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-27 10:00:00'));
        $user = User::factory()->create([
            'daily_goal_minna_lessons' => 1,
            'daily_goal_flashcards' => 10,
            'daily_study_minutes' => 20,
        ]);
        $this->seedWeeklyActivity($user);

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Mục tiêu tuần')
            ->assertSee('Học bài')
            ->assertSee('Ôn flashcard')
            ->assertSee('Làm quiz')
            ->assertSee('Giữ streak')
            ->assertSee('Gợi ý kế hoạch tuần sau');
    }

    public function test_weekly_goal_api_returns_progress_metrics(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-05-27 10:00:00'));
        $user = User::factory()->create([
            'daily_goal_minna_lessons' => 1,
            'daily_goal_flashcards' => 10,
            'daily_study_minutes' => 20,
        ]);
        $this->seedWeeklyActivity($user);
        Sanctum::actingAs($user);

        $this->getJson('/api/learning/weekly-goal')
            ->assertOk()
            ->assertJsonPath('metrics.lessons.completed', 2)
            ->assertJsonPath('metrics.lessons.target', 3)
            ->assertJsonPath('metrics.flashcards.completed', 3)
            ->assertJsonPath('metrics.quizzes.completed', 2)
            ->assertJsonPath('metrics.streak_days.completed', 3)
            ->assertJsonPath('next_week_plan.targets.streak_days', 5);
    }

    private function seedWeeklyActivity(User $user): void
    {
        $lessonA = $this->lesson(1);
        $lessonB = $this->lesson(2);
        $section = MinnaSection::query()->create([
            'lesson_id' => $lessonA->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Từ vựng',
            'content' => ['vocab' => [['tu_vung' => '駅', 'nghia' => 'nhà ga']]],
            'publish_status' => PublishStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        UserProgress::query()->create([
            'user_id' => $user->id,
            'lesson_type' => UserProgress::TYPE_MINNA,
            'lesson_id' => $lessonA->id,
            'status' => UserProgress::STATUS_COMPLETED,
            'last_accessed_at' => Carbon::parse('2026-05-25 09:00:00'),
            'completed_at' => Carbon::parse('2026-05-25 09:00:00'),
        ]);
        UserProgress::query()->create([
            'user_id' => $user->id,
            'lesson_type' => UserProgress::TYPE_MINNA,
            'lesson_id' => $lessonB->id,
            'status' => UserProgress::STATUS_COMPLETED,
            'last_accessed_at' => Carbon::parse('2026-05-26 09:00:00'),
            'completed_at' => Carbon::parse('2026-05-26 09:00:00'),
        ]);

        foreach (['2026-05-25 08:00:00', '2026-05-26 08:00:00', '2026-05-27 08:00:00'] as $date) {
            LearningEvent::query()->create([
                'user_id' => $user->id,
                'event_type' => LearningEvent::FLASHCARD_REVIEWED,
                'subject_type' => 'flashcard_card_state',
                'metadata' => [],
                'occurred_at' => Carbon::parse($date),
            ]);
        }

        foreach (['2026-05-26 10:00:00', '2026-05-27 10:00:00'] as $date) {
            LearningEvent::query()->create([
                'user_id' => $user->id,
                'event_type' => LearningEvent::QUIZ_SUBMITTED,
                'subject_type' => 'minna_quiz_attempt',
                'metadata' => [],
                'occurred_at' => Carbon::parse($date),
            ]);
        }

        MinnaQuizAttempt::query()->create([
            'user_id' => $user->id,
            'minna_lesson_id' => $lessonA->id,
            'score' => 4,
            'total' => 5,
            'percent' => 80,
            'passed' => true,
            'answers_snapshot' => [],
            'completed_at' => Carbon::parse('2026-05-27 10:00:00'),
        ]);

        FlashcardCardState::query()->create([
            'user_id' => $user->id,
            'minna_section_id' => $section->id,
            'card_index' => 0,
            'ease_factor' => 2.5,
            'repetitions' => 1,
            'interval_days' => 1,
            'next_review_at' => Carbon::parse('2026-05-28 10:00:00'),
            'last_reviewed_at' => Carbon::parse('2026-05-27 08:00:00'),
            'last_quality' => 4,
            'lapses' => 0,
        ]);
    }

    private function lesson(int $number): MinnaLesson
    {
        return MinnaLesson::query()->create([
            'number' => $number,
            'title' => 'Bài '.$number,
            'description' => 'Bài kiểm thử',
            'publish_status' => PublishStatus::PUBLISHED,
            'published_at' => now(),
        ]);
    }
}
