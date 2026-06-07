<?php

namespace Tests\Feature;

use App\Models\FavoriteItem;
use App\Models\Kanji;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_page_requires_at_least_two_characters(): void
    {
        $this->get(route('search.index', ['q' => 'a']))
            ->assertOk()
            ->assertSee('ít nhất 2 ký tự', false);
    }

    public function test_search_api_returns_grouped_results(): void
    {
        $lesson = MinnaLesson::create([
            'number' => 5,
            'title' => 'Đi mua sắm',
            'description' => 'Mua đồ',
            'publish_status' => 'published',
            'published_at' => now(),
        ]);

        MinnaSection::create([
            'lesson_id' => $lesson->id,
            'key' => 'tu-vung',
            'title' => 'Từ vựng',
            'order_index' => 1,
            'content' => [
                'vocab' => [
                    ['tu_vung' => '買い物', 'nghia' => 'mua sắm'],
                ],
                'mau_cau' => [
                    ['jp' => 'これをください', 'nghia' => 'Cho tôi cái này'],
                ],
            ],
            'publish_status' => 'published',
            'published_at' => now(),
        ]);

        MinnaSection::create([
            'lesson_id' => $lesson->id,
            'key' => 'ngu-phap',
            'title' => 'Ngữ pháp',
            'order_index' => 2,
            'content' => [
                ['title' => 'をください', 'pattern' => '買い物をください', 'explain' => ['Yêu cầu lịch sự']],
            ],
            'publish_status' => 'published',
            'published_at' => now(),
        ]);

        Kanji::create([
            'character' => '買',
            'meaning' => 'mua',
            'on_reading' => 'バイ',
            'kun_reading' => 'か',
            'level' => 'N5',
            'stroke_count' => 12,
            'radical' => '貝',
            'examples' => '買い物',
            'publish_status' => 'published',
            'published_at' => now(),
        ]);

        Cache::forget('global_search:minna_index:v1');

        $this->getJson(route('search.api', ['q' => '買']))
            ->assertOk()
            ->assertJsonPath('counts.vocabulary', 1)
            ->assertJsonPath('counts.kanji', 1)
            ->assertJsonPath('counts.grammar', 1);
    }

    public function test_authenticated_api_search_includes_favorites(): void
    {
        $user = User::factory()->create();
        FavoriteItem::create([
            'user_id' => $user->id,
            'item_key' => 'test-key',
            'item_type' => 'vocab',
            'front' => 'おはよう',
            'back' => 'chào buổi sáng',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/learning/search?q=おは')
            ->assertOk()
            ->assertJsonPath('counts.favorites', 1);
    }
}
