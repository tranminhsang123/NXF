<?php

namespace Tests\Feature;

use App\Models\FlashcardCardState;
use App\Models\MinnaLesson;
use App\Models\MinnaQuizAttempt;
use App\Models\MinnaSection;
use App\Models\User;
use App\Support\PublishStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class UserMistakesTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_mistakes_page_with_quiz_and_flashcard_data(): void
    {
        $user = User::factory()->create();
        $this->seedMistakeData($user);

        $this->actingAs($user)
            ->get(route('user.mistakes'))
            ->assertOk()
            ->assertSee('Lỗi sai của tôi')
            ->assertSee('Từ vựng hay sai')
            ->assertSee('ひと')
            ->assertSee('người')
            ->assertSee('N1 は N2 です')
            ->assertSee('Lộ trình sửa lỗi 5 phút/ngày');
    }

    public function test_mistakes_api_returns_summary_and_wrong_answers(): void
    {
        $user = User::factory()->create();
        $this->seedMistakeData($user);
        Sanctum::actingAs($user);

        $this->getJson('/api/learning/mistakes')
            ->assertOk()
            ->assertJsonPath('summary.wrong_quiz_count', 2)
            ->assertJsonPath('summary.weak_flashcard_count', 1)
            ->assertJsonCount(2, 'wrong_quiz_answers');
    }

    private function seedMistakeData(User $user): void
    {
        $lesson = MinnaLesson::query()->create([
            'number' => 1,
            'title' => 'Giới thiệu',
            'description' => 'Bài test lỗi sai',
            'publish_status' => PublishStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        $vocabSection = MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Từ vựng',
            'content' => [
                'vocab' => [
                    ['tu_vung' => 'ひと', 'han_tu' => '人', 'nghia' => 'người'],
                    ['tu_vung' => 'ほん', 'han_tu' => '本', 'nghia' => 'sách'],
                ],
            ],
            'publish_status' => PublishStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 2,
            'key' => 'ngu-phap',
            'title' => 'Ngữ pháp',
            'content' => [
                [
                    'title' => 'N1 は N2 です',
                    'explain' => 'Mẫu câu giới thiệu danh từ và thông tin cơ bản.',
                ],
            ],
            'publish_status' => PublishStatus::PUBLISHED,
            'published_at' => now(),
        ]);

        MinnaQuizAttempt::query()->create([
            'user_id' => $user->id,
            'minna_lesson_id' => $lesson->id,
            'score' => 1,
            'total' => 2,
            'percent' => 50,
            'passed' => false,
            'answers_snapshot' => [
                ['prompt' => 'ひと', 'answer' => 'người', 'selected' => 'sách', 'correct' => false],
                ['prompt' => 'ほん', 'answer' => 'sách', 'selected' => 'sách', 'correct' => true],
            ],
            'completed_at' => now()->subMinutes(5),
        ]);

        MinnaQuizAttempt::query()->create([
            'user_id' => $user->id,
            'minna_lesson_id' => $lesson->id,
            'score' => 0,
            'total' => 1,
            'percent' => 0,
            'passed' => false,
            'answers_snapshot' => [
                'mode' => 'advanced',
                'answers' => [
                    [
                        'type' => 'sentence_order',
                        'prompt' => 'Sắp xếp các mảnh sau thành câu đúng:',
                        'answer' => 'これは本です',
                        'selected' => '本これはです',
                        'correct' => false,
                    ],
                ],
            ],
            'completed_at' => now(),
        ]);

        FlashcardCardState::query()->create([
            'user_id' => $user->id,
            'minna_section_id' => $vocabSection->id,
            'card_index' => 0,
            'ease_factor' => 1.3,
            'repetitions' => 2,
            'interval_days' => 1,
            'next_review_at' => now()->subHour(),
            'last_reviewed_at' => now()->subDay(),
            'last_quality' => 1,
            'lapses' => 2,
        ]);
    }
}
