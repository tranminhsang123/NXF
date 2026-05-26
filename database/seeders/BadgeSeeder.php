<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['slug' => 'first_review', 'name' => 'Lần đầu ôn thẻ', 'description' => 'Hoàn thành lần đầu một lượt ôn flashcard SRS.', 'icon' => '📇', 'sort_order' => 10],
            ['slug' => 'first_minna_lesson', 'name' => 'Xong bài Minna đầu tiên', 'description' => 'Hoàn thành một bài Minna no Nihongo.', 'icon' => '📘', 'sort_order' => 20],
            ['slug' => 'streak_3', 'name' => 'Chuỗi 3 ngày', 'description' => 'Học liên tục 3 ngày.', 'icon' => '🔥', 'sort_order' => 30],
            ['slug' => 'streak_7', 'name' => 'Chuỗi 7 ngày', 'description' => 'Học liên tục 7 ngày.', 'icon' => '⚡', 'sort_order' => 40],
            ['slug' => 'xp_200', 'name' => '200 XP', 'description' => 'Tích lũy 200 điểm kinh nghiệm.', 'icon' => '⭐', 'sort_order' => 50],
            ['slug' => 'xp_500', 'name' => '500 XP', 'description' => 'Tích lũy 500 điểm kinh nghiệm.', 'icon' => '🌟', 'sort_order' => 60],
            ['slug' => 'vocab_50', 'name' => '50 từ đã ôn', 'description' => 'Có ít nhất 50 thẻ đã qua ít nhất một chu kỳ SRS.', 'icon' => '📚', 'sort_order' => 70],
        ];

        foreach ($rows as $row) {
            Badge::query()->updateOrCreate(
                ['slug' => $row['slug']],
                $row
            );
        }
    }
}
