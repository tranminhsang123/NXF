<?php

namespace Tests\Feature;

use App\Mail\StreakReminderMail;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\User;
use App\Services\PersonalizedRoadmapService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OnboardingRetentionTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_saves_onboarding_preferences(): void
    {
        $this->post(route('register.post'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'onboarding_level' => 'n5_started',
            'jlpt_goal' => 'N4',
            'daily_study_minutes' => 30,
            'learning_reasons' => ['jlpt', 'work'],
            'email_reminders_enabled' => '1',
        ])->assertRedirect(route('onboarding.result'));

        $user = User::query()->where('email', 'test@example.com')->firstOrFail();

        $this->assertSame('n5_started', $user->onboarding_level);
        $this->assertSame('N4', $user->jlpt_goal);
        $this->assertSame(30, (int) $user->daily_study_minutes);
        $this->assertSame(['jlpt', 'work'], $user->learning_reasons);
        $this->assertTrue((bool) $user->email_reminders_enabled);
        $this->assertNotNull($user->onboarding_completed_at);
        $this->assertNotNull($user->quick_win_started_at);
        $this->assertSame(1, (int) $user->daily_goal_minna_lessons);
        $this->assertSame(24, (int) $user->daily_goal_flashcards);
    }

    public function test_placement_test_overrides_self_declared_level(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $answers = collect(\App\Support\OnboardingOptions::placementQuestions())
            ->mapWithKeys(fn (array $question) => [$question['key'] => $question['answer']])
            ->all();

        $this->actingAs($user)
            ->post(route('onboarding.update'), [
                'onboarding_level' => 'new',
                'jlpt_goal' => 'N3',
                'daily_study_minutes' => 45,
                'learning_reasons' => ['anime', 'conversation'],
                'placement_answers' => $answers,
                'email_reminders_enabled' => '1',
            ])
            ->assertRedirect(route('onboarding.result'));

        $fresh = $user->fresh();
        $this->assertSame('n4_plus', $fresh->onboarding_level);
        $this->assertSame('n4_plus', $fresh->placement_test_level);
        $this->assertSame(12, (int) $fresh->placement_test_score);
        $this->assertSame(['anime', 'conversation'], $fresh->learning_reasons);
    }

    public function test_onboarding_sends_user_to_quick_win_lesson_when_content_exists(): void
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
            ->post(route('onboarding.update'), [
                'onboarding_level' => 'new',
                'jlpt_goal' => 'N5',
                'daily_study_minutes' => 20,
            ])
            ->assertRedirect(route('onboarding.result'));

        $this->assertNotNull($user->fresh()->quick_win_started_at);

        $this->actingAs($user)
            ->get(route('onboarding.result'))
            ->assertOk()
            ->assertSee('Kết quả placement test')
            ->assertSee('Bắt đầu bài 5 phút');
    }

    public function test_first_quick_win_completion_shows_congratulations_screen(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'onboarding_completed_at' => now(),
            'quick_win_started_at' => now(),
        ]);
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
            ->assertRedirect(route('quick-win.congrats', ['lesson' => $lesson->number, 'section' => $section->id]));

        $this->assertNotNull($user->fresh()->quick_win_completed_at);

        $this->actingAs($user)
            ->get(route('quick-win.congrats'))
            ->assertOk()
            ->assertSee('Quick win đầu tiên');
    }

    public function test_roadmap_uses_onboarding_level_for_first_recommendation(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'onboarding_level' => 'n5_started',
            'jlpt_goal' => 'N4',
            'daily_study_minutes' => 30,
            'onboarding_completed_at' => now(),
        ]);

        $lessonOne = MinnaLesson::query()->create(['number' => 1, 'title' => 'Bai 01']);
        $lessonSix = MinnaLesson::query()->create(['number' => 6, 'title' => 'Bai 06']);

        MinnaSection::query()->create([
            'lesson_id' => $lessonOne->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Tu vung 1',
            'content' => ['vocab' => []],
        ]);
        MinnaSection::query()->create([
            'lesson_id' => $lessonSix->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Tu vung 6',
            'content' => ['vocab' => []],
        ]);

        $roadmap = app(PersonalizedRoadmapService::class)->build($user);

        $this->assertSame(6, $roadmap['next_section']['lesson_number']);
        $this->assertStringContainsString('JLPT N4', $roadmap['reason']);
    }

    public function test_streak_reminder_command_sends_mail_and_notification(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => 'user',
            'current_streak' => 3,
            'last_study_date' => now()->subDay()->toDateString(),
            'email_reminders_enabled' => true,
            'onboarding_level' => 'new',
            'jlpt_goal' => 'N5',
            'daily_study_minutes' => 20,
            'onboarding_completed_at' => now(),
        ]);

        $lesson = MinnaLesson::query()->create(['number' => 1, 'title' => 'Bai 01']);
        MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Tu vung',
            'content' => ['vocab' => []],
        ]);

        $this->artisan('learning:send-streak-reminders')->assertSuccessful();

        Mail::assertSent(StreakReminderMail::class, fn (StreakReminderMail $mail) => $mail->hasTo($user->email));
        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'type' => 'streak_at_risk',
        ]);
        $this->assertNotNull($user->fresh()->last_study_reminder_sent_at);
    }
}
