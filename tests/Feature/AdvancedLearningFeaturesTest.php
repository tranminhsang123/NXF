<?php

namespace Tests\Feature;

use App\Models\Kanji;
use App\Models\MinnaLesson;
use App\Models\MinnaQuizAttempt;
use App\Models\MinnaSection;
use App\Models\PronunciationAudio;
use App\Models\User;
use App\Models\UserProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdvancedLearningFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dictionary_lookup_returns_minna_vocabulary_and_kanji(): void
    {
        $lesson = MinnaLesson::query()->create(['number' => 1, 'title' => 'Bai 01']);
        MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Tu vung',
            'content' => [
                'vocab' => [
                    ['tu_vung' => 'ほん', 'nghia' => 'sach', 'han_tu' => '本'],
                ],
            ],
        ]);
        Kanji::query()->create([
            'character' => '本',
            'meaning' => 'sach',
            'on_reading' => 'ホン',
            'kun_reading' => 'もと',
            'level' => 'N5',
            'stroke_count' => 5,
            'radical' => '木',
        ]);

        $response = $this->getJson(route('dictionary.lookup', ['q' => 'ほん']));

        $response->assertOk()
            ->assertJsonPath('entries.0.term', 'ほん')
            ->assertJsonPath('entries.0.lesson_number', 1);

        $this->getJson(route('dictionary.lookup', ['q' => '本']))
            ->assertOk()
            ->assertJsonPath('kanji.0.character', '本');
    }

    public function test_pronunciation_resolve_uses_cached_audio_when_available(): void
    {
        $text = 'こんにちは';
        $language = 'ja-JP';
        $service = app(\App\Services\PronunciationService::class);

        PronunciationAudio::query()->create([
            'text_hash' => $service->hash($text, $language),
            'text' => $text,
            'language' => $language,
            'source' => 'manual',
            'audio_url' => 'https://example.com/audio/konnichiwa.mp3',
        ]);

        $this->getJson(route('pronunciation.resolve', ['text' => $text]))
            ->assertOk()
            ->assertJsonPath('audio.audio_url', 'https://example.com/audio/konnichiwa.mp3')
            ->assertJsonPath('audio.provider', 'manual');
    }

    public function test_google_pronunciation_provider_generates_and_caches_audio(): void
    {
        Storage::fake('public');
        Http::fake([
            'texttospeech.googleapis.com/*' => Http::response([
                'audioContent' => base64_encode('fake-mp3-data'),
            ]),
        ]);

        config()->set('pronunciation.provider', 'google');
        config()->set('pronunciation.google.api_key', 'test-key');

        $text = 'konnichiwa';
        $language = 'ja-JP';
        $service = app(\App\Services\PronunciationService::class);

        $this->getJson(route('pronunciation.resolve', ['text' => $text]))
            ->assertOk()
            ->assertJsonPath('audio.provider', 'google')
            ->assertJsonPath('audio.fallback', null);

        Storage::disk('public')->assertExists('pronunciation/google-'.$service->hash($text, $language).'.mp3');
        $this->assertDatabaseHas('pronunciation_audios', [
            'text_hash' => $service->hash($text, $language),
            'source' => 'google',
        ]);
    }

    public function test_user_can_save_favorite_and_study_favorite_deck(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->postJson(route('favorites.store'), [
                'front' => 'konnichiwa',
                'back' => 'xin chao',
                'item_type' => 'vocabulary',
                'lesson_number' => 1,
            ])
            ->assertCreated()
            ->assertJsonPath('favorite.front', 'konnichiwa');

        $this->assertDatabaseHas('favorite_items', [
            'user_id' => $user->id,
            'front' => 'konnichiwa',
            'back' => 'xin chao',
        ]);

        $this->actingAs($user)
            ->get(route('flashcard.favorites'))
            ->assertOk()
            ->assertSee('konnichiwa');

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/flashcards/favorites')
            ->assertOk()
            ->assertJsonPath('cards.0.front', 'konnichiwa');
    }

    public function test_dashboard_exposes_charts_and_completion_forecast(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $lessons = collect([1, 2, 3, 4])->map(
            fn (int $number) => MinnaLesson::query()->create([
                'number' => $number,
                'title' => 'Bai '.$number,
            ])
        );

        foreach ($lessons->take(2) as $index => $lesson) {
            UserProgress::query()->create([
                'user_id' => $user->id,
                'lesson_type' => UserProgress::TYPE_MINNA,
                'lesson_id' => $lesson->id,
                'status' => UserProgress::STATUS_COMPLETED,
                'last_accessed_at' => now()->subDays($index),
                'completed_at' => now()->subDays($index),
            ]);
        }

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Biểu đồ tiến độ')
            ->assertSee('Dự báo hoàn thành');

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/learning/dashboard')
            ->assertOk()
            ->assertJsonPath('advancedDashboard.forecast.remaining_lessons', 2)
            ->assertJsonStructure([
                'advancedDashboard' => [
                    'charts' => [
                        'lessons_by_day' => ['labels', 'data'],
                        'lessons_by_week' => ['labels', 'data'],
                    ],
                    'forecast',
                ],
            ]);
    }

    public function test_dashboard_exposes_personalized_user_growth_modules(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'onboarding_completed_at' => now(),
            'learning_reasons' => ['travel'],
            'jlpt_goal' => 'N5',
            'daily_study_minutes' => 20,
            'last_study_date' => now()->subDays(4)->toDateString(),
        ]);
        $lesson = MinnaLesson::query()->create(['number' => 1, 'title' => 'Bai 01']);
        MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Tu vung',
            'content' => ['vocab' => []],
        ]);
        MinnaQuizAttempt::query()->create([
            'user_id' => $user->id,
            'minna_lesson_id' => $lesson->id,
            'score' => 2,
            'total' => 5,
            'percent' => 40,
            'passed' => false,
            'answers_snapshot' => [],
            'completed_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('user.dashboard'))
            ->assertOk()
            ->assertSee('Hôm nay học gì?')
            ->assertSee('Bài ôn 5 phút')
            ->assertSee('Từ vựng theo mục tiêu')
            ->assertSee('AI gợi ý bài yếu')
            ->assertSee('Ôn theo lỗi quiz');
    }

    public function test_leaderboard_share_and_study_room_pages_are_accessible(): void
    {
        $user = User::factory()->create([
            'role' => 'user',
            'xp_total' => 120,
            'current_streak' => 5,
        ]);

        $this->actingAs($user)
            ->get(route('leaderboard.index'))
            ->assertOk()
            ->assertSee('Bảng xếp hạng người học');

        $this->actingAs($user)
            ->get(route('achievements.share'))
            ->assertOk()
            ->assertSee('Card chia sẻ thành tích');

        $this->actingAs($user)
            ->get(route('study-room.index'))
            ->assertOk()
            ->assertSee('Phòng học nhóm realtime');
    }

    public function test_alphabet_page_includes_handwriting_practice_canvas(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->get(route('alphabet.index'))
            ->assertOk()
            ->assertSee('handwritingCanvas')
            ->assertSee('scoreHandwriting');
    }

    public function test_user_can_submit_advanced_quiz(): void
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
                    ['tu_vung' => 'ほん', 'nghia' => 'sach'],
                    ['tu_vung' => 'つくえ', 'nghia' => 'ban'],
                    ['tu_vung' => 'いす', 'nghia' => 'ghe'],
                    ['tu_vung' => 'わたし は 学生 です', 'nghia' => 'toi la hoc sinh'],
                ],
            ],
        ]);

        $this->actingAs($user)
            ->get(route('minna.quiz.advanced', ['number' => $lesson->number]))
            ->assertOk()
            ->assertSee('Quiz nâng cao');

        $this->actingAs($user)
            ->post(route('minna.quiz.advanced.submit', ['number' => $lesson->number]), [
                'answers' => [
                    'sach',
                    'ban',
                    'いす',
                    'わたしは学生です',
                ],
            ])
            ->assertRedirect(route('minna.show', ['number' => $lesson->number]));

        $this->assertSame(1, MinnaQuizAttempt::query()->count());
        $this->assertDatabaseHas('minna_quiz_attempts', [
            'user_id' => $user->id,
            'minna_lesson_id' => $lesson->id,
            'passed' => true,
        ]);
    }
}
