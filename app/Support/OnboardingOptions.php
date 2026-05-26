<?php

namespace App\Support;

use App\Models\User;

class OnboardingOptions
{
    public const DEFAULT_LEVEL = 'new';
    public const DEFAULT_GOAL = 'N5';
    public const DEFAULT_DAILY_MINUTES = 20;
    public const MIN_DAILY_MINUTES = 10;
    public const MAX_DAILY_MINUTES = 180;

    public static function levels(): array
    {
        return [
            'new' => 'Mới bắt đầu',
            'kana' => 'Đã biết Hiragana/Katakana',
            'n5_started' => 'Đã học một ít N5',
            'n5_review' => 'Đang ôn N5',
            'n4_plus' => 'N4 trở lên',
        ];
    }

    public static function goals(): array
    {
        return [
            'N5' => 'JLPT N5',
            'N4' => 'JLPT N4',
            'N3' => 'JLPT N3',
            'N2' => 'JLPT N2',
            'N1' => 'JLPT N1',
        ];
    }

    public static function learningReasons(): array
    {
        return [
            'travel' => 'Du lịch Nhật Bản',
            'work' => 'Làm việc với tiếng Nhật',
            'jlpt' => 'Thi JLPT',
            'anime' => 'Xem anime / manga',
            'study_abroad' => 'Du học / học văn hóa',
            'conversation' => 'Giao tiếp hằng ngày',
            'reading' => 'Đọc sách / tài liệu',
            'culture' => 'Tìm hiểu văn hóa Nhật',
        ];
    }

    public static function placementQuestions(): array
    {
        return [
            [
                'key' => 'kana_a',
                'prompt' => 'Chữ nào đọc là "a"?',
                'options' => ['あ', 'い', 'う', 'え'],
                'answer' => 'あ',
            ],
            [
                'key' => 'kana_ki',
                'prompt' => 'Chữ nào đọc là "ki"?',
                'options' => ['さ', 'き', 'ち', 'に'],
                'answer' => 'き',
            ],
            [
                'key' => 'watashi',
                'prompt' => '"わたし" nghĩa là gì?',
                'options' => ['Bạn', 'Tôi', 'Hôm nay', 'Trường học'],
                'answer' => 'Tôi',
            ],
            [
                'key' => 'topic_particle',
                'prompt' => 'Trợ từ đánh dấu chủ đề thường là?',
                'options' => ['を', 'に', 'は', 'で'],
                'answer' => 'は',
            ],
            [
                'key' => 'kore_hon',
                'prompt' => '"これは本です" nghĩa gần đúng là?',
                'options' => ['Đây là sách', 'Tôi đọc sách', 'Sách ở đâu', 'Đó là người'],
                'answer' => 'Đây là sách',
            ],
            [
                'key' => 'desu_past',
                'prompt' => 'Quá khứ lịch sự của "です" là?',
                'options' => ['でした', 'ではありません', 'ですた', 'だったり'],
                'answer' => 'でした',
            ],
            [
                'key' => 'doko',
                'prompt' => '"どこ" nghĩa là gì?',
                'options' => ['Khi nào', 'Ai', 'Ở đâu', 'Bao nhiêu'],
                'answer' => 'Ở đâu',
            ],
            [
                'key' => 'iku_te',
                'prompt' => 'Thể て của "行く" là?',
                'options' => ['行いて', '行って', '行んで', '行きて'],
                'answer' => '行って',
            ],
            [
                'key' => 'taberu_potential',
                'prompt' => 'Thể khả năng của "食べる" là?',
                'options' => ['食べます', '食べられる', '食べている', '食べたい'],
                'answer' => '食べられる',
            ],
            [
                'key' => 'must',
                'prompt' => '"〜なければなりません" dùng để nói gì?',
                'options' => ['Muốn làm', 'Đã từng làm', 'Phải làm', 'Không cần làm'],
                'answer' => 'Phải làm',
            ],
            [
                'key' => 'kanji_gaku',
                'prompt' => 'Kanji "学" liên quan đến nghĩa nào?',
                'options' => ['Ăn', 'Học', 'Mưa', 'Tiền'],
                'answer' => 'Học',
            ],
            [
                'key' => 'sou_desu',
                'prompt' => '"雨が降りそうです" nghĩa gần đúng là?',
                'options' => ['Trời đã mưa', 'Có vẻ sắp mưa', 'Không mưa', 'Tôi ghét mưa'],
                'answer' => 'Có vẻ sắp mưa',
            ],
        ];
    }

    public static function dailyMinuteOptions(): array
    {
        return [10, 20, 30, 45, 60];
    }

    public static function placementQuestionLevels(): array
    {
        return [
            'kana_a' => 'Kana',
            'kana_ki' => 'Kana',
            'watashi' => 'N5 nhập môn',
            'topic_particle' => 'N5 nhập môn',
            'kore_hon' => 'N5 nhập môn',
            'desu_past' => 'N5 nền tảng',
            'doko' => 'N5 nền tảng',
            'iku_te' => 'N5 nâng cao',
            'taberu_potential' => 'N4',
            'must' => 'N4',
            'kanji_gaku' => 'Kanji cơ bản',
            'sou_desu' => 'N4',
        ];
    }

    public static function placementBreakdown(?array $answers): array
    {
        $answers = $answers ?? [];
        $questionLevels = self::placementQuestionLevels();
        $groups = [];

        foreach (self::placementQuestions() as $question) {
            $level = $questionLevels[$question['key']] ?? 'Tổng hợp';
            $groups[$level] ??= [
                'label' => $level,
                'score' => 0,
                'answered' => 0,
                'total' => 0,
            ];

            $groups[$level]['total']++;
            $given = trim((string) ($answers[$question['key']] ?? ''));
            if ($given === '') {
                continue;
            }

            $groups[$level]['answered']++;
            if ($given === $question['answer']) {
                $groups[$level]['score']++;
            }
        }

        return array_values(array_map(function (array $group) {
            $group['percent'] = $group['total'] > 0
                ? (int) round(($group['score'] / $group['total']) * 100)
                : 0;

            return $group;
        }, $groups));
    }

    public static function levelKeys(): array
    {
        return array_keys(self::levels());
    }

    public static function goalKeys(): array
    {
        return array_keys(self::goals());
    }

    public static function learningReasonKeys(): array
    {
        return array_keys(self::learningReasons());
    }

    public static function levelLabel(?string $level): string
    {
        return self::levels()[$level ?: self::DEFAULT_LEVEL] ?? self::levels()[self::DEFAULT_LEVEL];
    }

    public static function goalLabel(?string $goal): string
    {
        return self::goals()[$goal ?: self::DEFAULT_GOAL] ?? self::goals()[self::DEFAULT_GOAL];
    }

    public static function normalizeDailyMinutes(mixed $minutes): int
    {
        $value = (int) $minutes;
        if ($value <= 0) {
            $value = self::DEFAULT_DAILY_MINUTES;
        }

        return max(self::MIN_DAILY_MINUTES, min(self::MAX_DAILY_MINUTES, $value));
    }

    public static function dailyGoalsFromMinutes(int $minutes): array
    {
        $minutes = self::normalizeDailyMinutes($minutes);

        return [
            'daily_goal_minna_lessons' => $minutes >= 45 ? 2 : 1,
            'daily_goal_flashcards' => max(8, min(50, (int) round($minutes * 0.8))),
        ];
    }

    public static function startLessonNumber(?string $level): int
    {
        return match ($level) {
            'n5_started' => 6,
            'n5_review' => 15,
            'n4_plus' => 25,
            default => 1,
        };
    }

    public static function evaluatePlacement(array $answers): array
    {
        $questions = self::placementQuestions();
        $normalized = [];
        $score = 0;

        foreach ($questions as $question) {
            $key = $question['key'];
            $answer = trim((string) ($answers[$key] ?? ''));
            if ($answer === '') {
                continue;
            }

            $normalized[$key] = $answer;
            if ($answer === $question['answer']) {
                $score++;
            }
        }

        if ($normalized === []) {
            return [
                'answered' => false,
                'score' => null,
                'level' => null,
                'answers' => null,
            ];
        }

        $level = match (true) {
            $score >= 10 => 'n4_plus',
            $score >= 7 => 'n5_review',
            $score >= 4 => 'n5_started',
            $score >= 2 => 'kana',
            default => 'new',
        };

        return [
            'answered' => true,
            'score' => $score,
            'level' => $level,
            'answers' => $normalized,
        ];
    }

    public static function normalizeLearningReasons(array $reasons): array
    {
        $allowed = array_flip(self::learningReasonKeys());

        return collect($reasons)
            ->map(fn ($reason) => (string) $reason)
            ->filter(fn (string $reason) => isset($allowed[$reason]))
            ->unique()
            ->values()
            ->all();
    }

    public static function reasonLabels(array $reasons): array
    {
        $labels = self::learningReasons();

        return collect($reasons)
            ->map(fn (string $reason) => $labels[$reason] ?? null)
            ->filter()
            ->values()
            ->all();
    }

    public static function reasonFocusText(array $reasons): ?string
    {
        if (in_array('jlpt', $reasons, true)) {
            return 'Bạn chọn mục tiêu thi JLPT nên hệ thống ưu tiên lộ trình có quiz và ôn tập đều.';
        }
        if (in_array('travel', $reasons, true) || in_array('conversation', $reasons, true)) {
            return 'Bạn ưu tiên giao tiếp nên hệ thống sẽ gợi ý nhiều từ/câu dùng ngay trong tình huống hằng ngày.';
        }
        if (in_array('anime', $reasons, true) || in_array('culture', $reasons, true)) {
            return 'Bạn học vì sở thích văn hóa nên hệ thống sẽ ưu tiên ví dụ dễ nhận diện và từ vựng gần đời sống.';
        }
        if (in_array('work', $reasons, true) || in_array('study_abroad', $reasons, true)) {
            return 'Bạn học cho công việc/học tập nên hệ thống sẽ giữ nhịp bài mới và ôn lại phần dễ quên.';
        }

        return null;
    }

    public static function preferencesForCreate(array $data, bool $completed = true): array
    {
        $level = in_array($data['onboarding_level'] ?? null, self::levelKeys(), true)
            ? $data['onboarding_level']
            : self::DEFAULT_LEVEL;
        $goal = in_array($data['jlpt_goal'] ?? null, self::goalKeys(), true)
            ? $data['jlpt_goal']
            : self::DEFAULT_GOAL;
        $minutes = self::normalizeDailyMinutes($data['daily_study_minutes'] ?? self::DEFAULT_DAILY_MINUTES);
        $reasons = self::normalizeLearningReasons($data['learning_reasons'] ?? []);
        $placement = [
            'score' => $data['placement_test_score'] ?? null,
            'level' => $data['placement_test_level'] ?? null,
            'answers' => $data['placement_answers'] ?? null,
        ];

        return array_merge(
            [
                'onboarding_level' => $level,
                'jlpt_goal' => $goal,
                'daily_study_minutes' => $minutes,
                'learning_reasons' => $reasons,
                'placement_test_score' => $placement['score'],
                'placement_test_level' => $placement['level'],
                'placement_answers' => $placement['answers'],
                'email_reminders_enabled' => (bool) ($data['email_reminders_enabled'] ?? true),
                'onboarding_completed_at' => $completed ? now() : null,
            ],
            self::dailyGoalsFromMinutes($minutes)
        );
    }

    public static function summaryFor(User $user): array
    {
        $level = $user->onboarding_level ?: self::DEFAULT_LEVEL;
        $goal = $user->jlpt_goal ?: self::DEFAULT_GOAL;
        $minutes = self::normalizeDailyMinutes($user->daily_study_minutes ?? self::DEFAULT_DAILY_MINUTES);
        $reasons = self::normalizeLearningReasons($user->learning_reasons ?? []);

        return [
            'level' => $level,
            'level_label' => self::levelLabel($level),
            'jlpt_goal' => $goal,
            'jlpt_goal_label' => self::goalLabel($goal),
            'daily_study_minutes' => $minutes,
            'learning_reasons' => $reasons,
            'learning_reason_labels' => self::reasonLabels($reasons),
            'reason_focus_text' => self::reasonFocusText($reasons),
            'placement_test_score' => $user->placement_test_score,
            'placement_test_level' => $user->placement_test_level,
            'placement_test_level_label' => self::levelLabel($user->placement_test_level),
            'email_reminders_enabled' => (bool) ($user->email_reminders_enabled ?? true),
            'completed' => $user->onboarding_completed_at !== null,
        ];
    }
}
