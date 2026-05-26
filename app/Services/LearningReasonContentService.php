<?php

namespace App\Services;

use App\Models\User;
use App\Support\OnboardingOptions;

class LearningReasonContentService
{
    private const VOCABULARY = [
        'travel' => [
            ['jp' => '駅', 'reading' => 'えき', 'meaning' => 'nhà ga', 'example' => '駅はどこですか。', 'tag' => 'Du lịch'],
            ['jp' => '予約', 'reading' => 'よやく', 'meaning' => 'đặt trước', 'example' => '予約があります。', 'tag' => 'Du lịch'],
            ['jp' => '切符', 'reading' => 'きっぷ', 'meaning' => 'vé', 'example' => '切符をください。', 'tag' => 'Du lịch'],
            ['jp' => 'おすすめ', 'reading' => 'おすすめ', 'meaning' => 'gợi ý / món nên thử', 'example' => 'おすすめは何ですか。', 'tag' => 'Du lịch'],
        ],
        'work' => [
            ['jp' => '会議', 'reading' => 'かいぎ', 'meaning' => 'cuộc họp', 'example' => '会議は何時ですか。', 'tag' => 'Công việc'],
            ['jp' => '資料', 'reading' => 'しりょう', 'meaning' => 'tài liệu', 'example' => '資料を送ります。', 'tag' => 'Công việc'],
            ['jp' => '確認', 'reading' => 'かくにん', 'meaning' => 'xác nhận', 'example' => '確認してください。', 'tag' => 'Công việc'],
            ['jp' => '締切', 'reading' => 'しめきり', 'meaning' => 'hạn chót', 'example' => '締切は金曜日です。', 'tag' => 'Công việc'],
        ],
        'jlpt' => [
            ['jp' => '文法', 'reading' => 'ぶんぽう', 'meaning' => 'ngữ pháp', 'example' => '文法を復習します。', 'tag' => 'JLPT'],
            ['jp' => '問題', 'reading' => 'もんだい', 'meaning' => 'câu hỏi / vấn đề', 'example' => '問題を解きます。', 'tag' => 'JLPT'],
            ['jp' => '復習', 'reading' => 'ふくしゅう', 'meaning' => 'ôn lại', 'example' => '毎日復習します。', 'tag' => 'JLPT'],
            ['jp' => '合格', 'reading' => 'ごうかく', 'meaning' => 'đỗ kỳ thi', 'example' => 'JLPTに合格したいです。', 'tag' => 'JLPT'],
        ],
        'anime' => [
            ['jp' => '大丈夫', 'reading' => 'だいじょうぶ', 'meaning' => 'ổn / không sao', 'example' => '大丈夫ですか。', 'tag' => 'Anime'],
            ['jp' => '本当', 'reading' => 'ほんとう', 'meaning' => 'thật không', 'example' => '本当ですか。', 'tag' => 'Anime'],
            ['jp' => 'すごい', 'reading' => 'すごい', 'meaning' => 'tuyệt / ghê thật', 'example' => 'すごいですね。', 'tag' => 'Anime'],
            ['jp' => '約束', 'reading' => 'やくそく', 'meaning' => 'lời hứa', 'example' => '約束します。', 'tag' => 'Anime'],
        ],
        'conversation' => [
            ['jp' => '久しぶり', 'reading' => 'ひさしぶり', 'meaning' => 'lâu rồi không gặp', 'example' => '久しぶりですね。', 'tag' => 'Giao tiếp'],
            ['jp' => 'どう思いますか', 'reading' => 'どうおもいますか', 'meaning' => 'bạn nghĩ sao', 'example' => 'これをどう思いますか。', 'tag' => 'Giao tiếp'],
            ['jp' => 'もう一度', 'reading' => 'もういちど', 'meaning' => 'một lần nữa', 'example' => 'もう一度お願いします。', 'tag' => 'Giao tiếp'],
        ],
        'study_abroad' => [
            ['jp' => '授業', 'reading' => 'じゅぎょう', 'meaning' => 'giờ học', 'example' => '授業は九時からです。', 'tag' => 'Du học'],
            ['jp' => '寮', 'reading' => 'りょう', 'meaning' => 'ký túc xá', 'example' => '寮に住んでいます。', 'tag' => 'Du học'],
            ['jp' => '先生', 'reading' => 'せんせい', 'meaning' => 'giáo viên', 'example' => '先生に聞きます。', 'tag' => 'Du học'],
        ],
        'reading' => [
            ['jp' => '意味', 'reading' => 'いみ', 'meaning' => 'ý nghĩa', 'example' => '意味を調べます。', 'tag' => 'Đọc hiểu'],
            ['jp' => '文章', 'reading' => 'ぶんしょう', 'meaning' => 'đoạn văn', 'example' => '文章を読みます。', 'tag' => 'Đọc hiểu'],
            ['jp' => '要点', 'reading' => 'ようてん', 'meaning' => 'ý chính', 'example' => '要点を書きます。', 'tag' => 'Đọc hiểu'],
        ],
        'culture' => [
            ['jp' => '祭り', 'reading' => 'まつり', 'meaning' => 'lễ hội', 'example' => '祭りに行きます。', 'tag' => 'Văn hóa'],
            ['jp' => '温泉', 'reading' => 'おんせん', 'meaning' => 'suối nước nóng', 'example' => '温泉が好きです。', 'tag' => 'Văn hóa'],
            ['jp' => '神社', 'reading' => 'じんじゃ', 'meaning' => 'đền thần đạo', 'example' => '神社を見ます。', 'tag' => 'Văn hóa'],
        ],
    ];

    public function profileFor(User $user): array
    {
        $reasons = OnboardingOptions::normalizeLearningReasons($user->learning_reasons ?? []);
        if ($reasons === []) {
            $reasons = ['jlpt'];
        }

        $primary = $this->primaryReason($reasons);
        $vocabulary = collect($reasons)
            ->flatMap(fn (string $reason) => self::VOCABULARY[$reason] ?? [])
            ->unique('jp')
            ->take(8)
            ->values()
            ->all();

        if ($vocabulary === []) {
            $vocabulary = self::VOCABULARY['jlpt'];
        }

        return [
            'primary_reason' => $primary,
            'labels' => OnboardingOptions::reasonLabels($reasons),
            'focus_text' => OnboardingOptions::reasonFocusText($reasons) ?: $this->focusText($primary),
            'reminder_message' => $this->reminderMessageFor($user),
            'vocabulary' => $vocabulary,
            'mini_lesson' => $this->miniLessonFor($primary),
        ];
    }

    public function reminderMessageFor(User $user): string
    {
        $reasons = OnboardingOptions::normalizeLearningReasons($user->learning_reasons ?? []);
        $primary = $this->primaryReason($reasons ?: ['jlpt']);

        return match ($primary) {
            'travel' => 'Ôn 5 phút hôm nay để lúc đi Nhật hỏi đường, đặt vé và gọi món tự tin hơn.',
            'work' => 'Giữ nhịp học hôm nay để vốn từ công việc không bị rơi khỏi trí nhớ.',
            'anime' => 'Một lượt nghe và ôn ngắn hôm nay sẽ giúp bạn bắt được thêm câu thoại quen.',
            'conversation' => 'Ôn vài mẫu câu giao tiếp hôm nay để nói tự nhiên hơn khi cần dùng thật.',
            'study_abroad' => 'Giữ streak hôm nay để lộ trình du học không bị ngắt quãng.',
            'reading' => 'Ôn vài từ khóa hôm nay để đọc đoạn ngắn nhẹ hơn vào lần tới.',
            'culture' => 'Học một chút hôm nay để hiểu thêm các tình huống văn hóa Nhật thường gặp.',
            default => 'Ôn 5 phút hôm nay để giữ đà thi JLPT và không quên phần vừa học.',
        };
    }

    private function primaryReason(array $reasons): string
    {
        foreach (['jlpt', 'travel', 'work', 'anime', 'conversation', 'study_abroad', 'reading', 'culture'] as $candidate) {
            if (in_array($candidate, $reasons, true)) {
                return $candidate;
            }
        }

        return $reasons[0] ?? 'jlpt';
    }

    private function focusText(string $reason): string
    {
        return match ($reason) {
            'travel' => 'Hệ thống ưu tiên từ vựng tình huống đi lại, nhà hàng và khách sạn.',
            'work' => 'Hệ thống ưu tiên câu lịch sự, email ngắn và từ vựng cuộc họp.',
            'anime' => 'Hệ thống ưu tiên câu thoại ngắn, từ hay gặp và luyện nghe nhanh.',
            'conversation' => 'Hệ thống ưu tiên phản xạ hỏi đáp và mẫu câu dùng hằng ngày.',
            default => 'Hệ thống ưu tiên bài có quiz, ôn lỗi sai và nhịp học đều để phục vụ JLPT.',
        };
    }

    private function miniLessonFor(string $reason): array
    {
        return match ($reason) {
            'travel' => [
                'title' => 'Bài 5 phút: hỏi đường và gọi món',
                'steps' => ['Nghe 3 từ vựng du lịch', 'Đọc to 2 mẫu câu hỏi đường', 'Làm 1 quiz chọn nghĩa nhanh'],
            ],
            'work' => [
                'title' => 'Bài 5 phút: mở đầu cuộc họp',
                'steps' => ['Ôn 4 từ công việc', 'Đọc 1 câu nhờ xác nhận', 'Tự viết một câu lịch sự ngắn'],
            ],
            'anime' => [
                'title' => 'Bài 5 phút: bắt câu thoại quen',
                'steps' => ['Nghe 3 câu ngắn', 'Nhắc lại phát âm', 'Lưu 1 từ muốn nhớ lâu'],
            ],
            'conversation' => [
                'title' => 'Bài 5 phút: hỏi đáp tự nhiên',
                'steps' => ['Ôn lời chào', 'Luyện hỏi lại khi chưa nghe rõ', 'Chọn câu trả lời phù hợp'],
            ],
            default => [
                'title' => 'Bài 5 phút: ôn lỗi dễ mất điểm JLPT',
                'steps' => ['Ôn 3 từ vựng', 'Làm 2 câu quiz', 'Đánh dấu phần cần ôn lại'],
            ],
        };
    }
}
