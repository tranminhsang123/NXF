<?php

namespace Tests\Feature;

use App\Models\LearningEvent;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LearningEventTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_minna_learning_actions_are_tracked(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        [$lesson, $section] = $this->createLessonWithVocabulary();

        $this->actingAs($user)
            ->get(route('minna.show', ['number' => $lesson->number]))
            ->assertOk();

        $this->assertDatabaseHas('learning_events', [
            'user_id' => $user->id,
            'event_type' => LearningEvent::LESSON_VIEWED,
            'minna_lesson_id' => $lesson->id,
        ]);

        $this->actingAs($user)
            ->get(route('minna.section', ['number' => $lesson->number, 'sectionKey' => $section->key]))
            ->assertOk();

        $this->assertDatabaseHas('learning_events', [
            'user_id' => $user->id,
            'event_type' => LearningEvent::SECTION_VIEWED,
            'minna_section_id' => $section->id,
        ]);

        $this->actingAs($user)
            ->post(route('minna.section.complete', ['number' => $lesson->number, 'section' => $section->id]))
            ->assertRedirect();

        $this->assertDatabaseHas('learning_events', [
            'user_id' => $user->id,
            'event_type' => LearningEvent::SECTION_COMPLETED,
            'minna_section_id' => $section->id,
        ]);

        $this->actingAs($user)
            ->post(route('minna.quiz.submit', ['number' => $lesson->number]), [
                'answers' => ['sach', 'ban', 'ghe', 'but', 'nuoc'],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('learning_events', [
            'user_id' => $user->id,
            'event_type' => LearningEvent::QUIZ_SUBMITTED,
            'minna_lesson_id' => $lesson->id,
        ]);
    }

    public function test_flashcard_favorite_dictionary_and_frontend_events_are_tracked(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        [$lesson, $section] = $this->createLessonWithVocabulary();

        $this->actingAs($user)
            ->get(route('flashcard.study', ['number' => $lesson->number]))
            ->assertOk();

        $this->assertDatabaseHas('learning_events', [
            'user_id' => $user->id,
            'event_type' => LearningEvent::FLASHCARD_DECK_OPENED,
        ]);

        $this->actingAs($user)
            ->postJson(route('flashcard.review'), [
                'minna_section_id' => $section->id,
                'card_index' => 0,
                'quality' => 4,
            ])
            ->assertOk();

        $this->assertDatabaseHas('learning_events', [
            'user_id' => $user->id,
            'event_type' => LearningEvent::FLASHCARD_REVIEWED,
            'minna_section_id' => $section->id,
        ]);

        $this->actingAs($user)
            ->postJson(route('favorites.store'), [
                'front' => 'ほん',
                'back' => 'sach',
                'item_type' => 'vocabulary',
                'lesson_number' => $lesson->number,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('learning_events', [
            'user_id' => $user->id,
            'event_type' => LearningEvent::FAVORITE_SAVED,
        ]);

        $this->actingAs($user)
            ->getJson(route('dictionary.lookup', ['q' => 'ほん']))
            ->assertOk();

        $this->assertDatabaseHas('learning_events', [
            'user_id' => $user->id,
            'event_type' => LearningEvent::DICTIONARY_LOOKUP,
        ]);

        $this->actingAs($user)
            ->postJson(route('learning-events.store'), [
                'event_type' => LearningEvent::AUDIO_PLAYED,
                'minna_lesson_id' => $lesson->id,
                'minna_section_id' => $section->id,
                'metadata' => [
                    'text' => 'ほん',
                    'source' => 'vocabulary_button',
                ],
            ])
            ->assertCreated()
            ->assertJsonPath('ok', true);

        $this->assertDatabaseHas('learning_events', [
            'user_id' => $user->id,
            'event_type' => LearningEvent::AUDIO_PLAYED,
            'minna_lesson_id' => $lesson->id,
            'minna_section_id' => $section->id,
        ]);
    }

    private function createLessonWithVocabulary(): array
    {
        $lesson = MinnaLesson::query()->create(['number' => 1, 'title' => 'Bai 01']);
        $section = MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Tu vung',
            'content' => [
                'vocab' => [
                    ['tu_vung' => 'ほん', 'nghia' => 'sach'],
                    ['tu_vung' => 'つくえ', 'nghia' => 'ban'],
                    ['tu_vung' => 'いす', 'nghia' => 'ghe'],
                    ['tu_vung' => 'えんぴつ', 'nghia' => 'but'],
                    ['tu_vung' => 'みず', 'nghia' => 'nuoc'],
                ],
            ],
        ]);

        return [$lesson, $section];
    }
}
