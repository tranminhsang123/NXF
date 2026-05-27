<?php

namespace Tests\Feature;

use App\Models\ContentVersion;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class AdminContentStudioTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_lesson_from_content_studio_template(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get(route('admin.content-studio.index'))
            ->assertOk()
            ->assertSee('Xưởng nội dung');

        $this->actingAs($admin)
            ->post(route('admin.content-studio.template'), [
                'number' => 77,
                'title' => 'Bài template mới',
                'description' => 'Soạn nhanh từ Studio',
            ])
            ->assertRedirect(route('admin.content-studio.index', ['q' => 77]));

        $lesson = MinnaLesson::query()->where('number', 77)->firstOrFail();

        $this->assertSame('draft', $lesson->publish_status);
        $this->assertSame(5, $lesson->sections()->count());
        $this->assertDatabaseHas('content_versions', [
            'versionable_type' => MinnaLesson::class,
            'versionable_id' => $lesson->id,
        ]);
    }

    public function test_admin_can_import_csv_into_lesson_sections(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $csv = implode("\n", [
            'lesson_number,title,section_key,group,jp,nghia,romaji',
            '78,Bài import,tu-vung,vocab,あさ,buổi sáng,asa',
            '78,Bài import,tu-vung,vocab,ひる,buổi trưa,hiru',
        ]);

        $file = UploadedFile::fake()->createWithContent('minna-import.csv', $csv);

        $this->actingAs($admin)
            ->post(route('admin.content-studio.import'), [
                'file' => $file,
            ])
            ->assertRedirect(route('admin.content-studio.index'));

        $lesson = MinnaLesson::query()->where('number', 78)->firstOrFail();
        $section = $lesson->sections()->where('key', 'tu-vung')->firstOrFail();

        $this->assertSame('Bài import', $lesson->title);
        $this->assertCount(2, $section->content['vocab']);
        $this->assertSame('あさ', $section->content['vocab'][0]['tu_vung']);
    }

    public function test_admin_can_generate_quiz_flashcards_preview_and_compare_versions(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $lesson = $this->createLessonWithVocabulary();

        ContentVersion::query()->create([
            'versionable_type' => MinnaLesson::class,
            'versionable_id' => $lesson->id,
            'actor_id' => $admin->id,
            'action' => 'updated',
            'snapshot' => [
                'number' => $lesson->number,
                'title' => 'Tiêu đề cũ',
                'description' => 'Mô tả cũ',
                'publish_status' => 'draft',
                'published_at' => null,
                'archived_at' => null,
            ],
            'changes' => null,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.content-studio.generate-quiz', $lesson))
            ->assertRedirect(route('admin.content-studio.index', ['q' => $lesson->number]));

        $this->actingAs($admin)
            ->post(route('admin.content-studio.generate-flashcards', $lesson))
            ->assertRedirect(route('admin.content-studio.index', ['q' => $lesson->number]));

        $quiz = $lesson->fresh()->sections()->where('key', 'quiz')->firstOrFail();
        $flashcards = $lesson->fresh()->sections()->where('key', 'flashcards')->firstOrFail();

        $this->assertCount(4, $quiz->content['mini_quiz']);
        $this->assertGreaterThanOrEqual(3, count($quiz->content['advanced_quiz']));
        $this->assertCount(4, $flashcards->content['cards']);

        $this->actingAs($admin)
            ->get(route('admin.content-studio.preview', $lesson))
            ->assertOk()
            ->assertSee('Xem như người học thật')
            ->assertSee('Flashcard sinh từ bài');

        $this->actingAs($admin)
            ->get(route('admin.content-studio.compare', $lesson))
            ->assertOk()
            ->assertSee('So sánh version trước / sau')
            ->assertSee('Tiêu đề cũ');
    }

    private function createLessonWithVocabulary(): MinnaLesson
    {
        $lesson = MinnaLesson::query()->create([
            'number' => 79,
            'title' => 'Bài có dữ liệu Studio',
            'description' => 'Đủ từ để sinh quiz',
            'publish_status' => 'draft',
        ]);

        MinnaSection::query()->create([
            'lesson_id' => $lesson->id,
            'order_index' => 1,
            'key' => 'tu-vung',
            'title' => 'Từ vựng',
            'publish_status' => 'published',
            'content' => [
                'vocab' => [
                    ['tu_vung' => 'あさ', 'nghia' => 'buổi sáng'],
                    ['tu_vung' => 'ひる', 'nghia' => 'buổi trưa'],
                    ['tu_vung' => 'よる', 'nghia' => 'buổi tối'],
                    ['tu_vung' => 'ほん', 'nghia' => 'sách'],
                ],
            ],
        ]);

        return $lesson->fresh('sections');
    }
}
