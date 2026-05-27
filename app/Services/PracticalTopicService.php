<?php

namespace App\Services;

use App\Models\User;
use App\Support\OnboardingOptions;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PracticalTopicService
{
    public function all(): Collection
    {
        return collect($this->topics())
            ->map(fn (array $topic) => $this->withDerivedData($topic))
            ->values();
    }

    public function find(string $slug): ?array
    {
        $topic = collect($this->topics())->firstWhere('slug', $slug);

        return $topic ? $this->withDerivedData($topic) : null;
    }

    public function recommendedFor(?User $user): array
    {
        $topics = $this->all();
        $reasons = $user ? OnboardingOptions::normalizeLearningReasons($user->learning_reasons ?? []) : [];
        $preferredTags = $this->preferredTags($reasons);

        $recommended = $topics
            ->sortByDesc(function (array $topic) use ($preferredTags) {
                return collect($topic['tags'])->intersect($preferredTags)->count();
            })
            ->take(3)
            ->values();

        if ($recommended->every(fn (array $topic) => collect($topic['tags'])->intersect($preferredTags)->isEmpty())) {
            $recommended = $topics->take(3)->values();
        }

        return $recommended->all();
    }

    public function summaryFor(?User $user): array
    {
        $topics = $this->all();
        $recommended = $this->recommendedFor($user);

        return [
            'total_topics' => $topics->count(),
            'total_vocabulary' => $topics->sum(fn (array $topic) => count($topic['vocabulary'])),
            'total_dialogues' => $topics->sum(fn (array $topic) => count($topic['dialogue'])),
            'recommended' => $recommended,
            'index_url' => route('topics.index'),
        ];
    }

    public function grade(string $slug, array $answers): ?array
    {
        $topic = $this->find($slug);
        if (! $topic) {
            return null;
        }

        $questions = $topic['quiz'];
        $correct = 0;
        $rows = [];

        foreach ($questions as $question) {
            $id = (string) $question['id'];
            $selected = trim((string) ($answers[$id] ?? ''));
            $answer = (string) $question['answer'];
            $isCorrect = $this->normalizeAnswer($selected) === $this->normalizeAnswer($answer);

            if ($isCorrect) {
                $correct++;
            }

            $rows[] = [
                'id' => $id,
                'prompt' => $question['prompt'],
                'selected' => $selected,
                'answer' => $answer,
                'correct' => $isCorrect,
                'explanation' => $question['explanation'] ?? null,
            ];
        }

        $total = count($questions);
        $percent = $total > 0 ? (int) round(($correct / $total) * 100) : 0;

        return [
            'topic_slug' => $slug,
            'score' => $correct,
            'total' => $total,
            'percent' => $percent,
            'passed' => $percent >= 70,
            'answers' => $rows,
        ];
    }

    private function withDerivedData(array $topic): array
    {
        $topic['flashcards'] = $this->flashcardsFor($topic);
        $topic['first_audio_text'] = $topic['dialogue'][0]['jp'] ?? ($topic['patterns'][0]['jp'] ?? '');
        $topic['url'] = route('topics.show', ['slug' => $topic['slug']]);
        $topic['quiz_url'] = route('topics.quiz', ['slug' => $topic['slug']]);

        return $topic;
    }

    private function flashcardsFor(array $topic): array
    {
        $vocabularyCards = collect($topic['vocabulary'])
            ->take(6)
            ->map(fn (array $item) => [
                'front' => $item['jp'],
                'back' => trim($item['reading'].' - '.$item['meaning']),
                'hint' => $item['example'] ?? null,
            ]);

        $patternCards = collect($topic['patterns'])
            ->take(3)
            ->map(fn (array $item) => [
                'front' => $item['jp'],
                'back' => $item['meaning'],
                'hint' => $item['example'] ?? null,
            ]);

        return $vocabularyCards->merge($patternCards)->values()->all();
    }

    private function preferredTags(array $reasons): array
    {
        $tags = [];

        foreach ($reasons as $reason) {
            $tags = array_merge($tags, match ($reason) {
                'travel' => ['du_lich', 'nha_hang', 'khach_san'],
                'work' => ['cong_viec', 'cong_so', 'email'],
                'anime' => ['anime'],
                'conversation' => ['doi_song', 'hoi_thoai'],
                'study_abroad' => ['du_hoc', 'doi_song'],
                default => ['hoi_thoai'],
            });
        }

        return array_values(array_unique($tags ?: ['hoi_thoai']));
    }

    private function normalizeAnswer(string $value): string
    {
        return Str::of($value)
            ->lower()
            ->replaceMatches('/\s+/u', '')
            ->replace(['。', '.', '！', '!', '？', '?'], '')
            ->toString();
    }

    private function topics(): array
    {
        return [
            [
                'slug' => 'di-du-lich-nhat',
                'title' => 'Đi du lịch Nhật',
                'subtitle' => 'Các câu cần dùng khi mua vé, hỏi thông tin và xử lý tình huống ở ga.',
                'level' => 'N5-N4',
                'duration_minutes' => 12,
                'tags' => ['du_lich', 'hoi_thoai'],
                'goal' => 'Tự hỏi thông tin cơ bản khi đi tàu hoặc tham quan.',
                'vocabulary' => [
                    ['jp' => '駅', 'reading' => 'えき', 'meaning' => 'nhà ga', 'example' => '駅はどこですか。'],
                    ['jp' => '切符', 'reading' => 'きっぷ', 'meaning' => 'vé', 'example' => '切符をください。'],
                    ['jp' => '出口', 'reading' => 'でぐち', 'meaning' => 'lối ra', 'example' => '出口はどこですか。'],
                    ['jp' => '観光案内所', 'reading' => 'かんこうあんないじょ', 'meaning' => 'quầy hướng dẫn du lịch', 'example' => '観光案内所へ行きたいです。'],
                    ['jp' => '地図', 'reading' => 'ちず', 'meaning' => 'bản đồ', 'example' => '地図がありますか。'],
                ],
                'patterns' => [
                    ['jp' => '〜へ行きたいです。', 'meaning' => 'Tôi muốn đi tới ~.', 'usage' => 'Dùng khi nói điểm đến.', 'example' => '京都へ行きたいです。'],
                    ['jp' => '〜はどこですか。', 'meaning' => '~ ở đâu?', 'usage' => 'Dùng để hỏi vị trí.', 'example' => '出口はどこですか。'],
                    ['jp' => '〜をください。', 'meaning' => 'Cho tôi ~.', 'usage' => 'Dùng khi mua hoặc xin đồ.', 'example' => '切符をください。'],
                ],
                'dialogue' => [
                    ['speaker' => 'A', 'jp' => 'すみません。京都へ行きたいです。', 'vi' => 'Xin lỗi. Tôi muốn đi Kyoto.'],
                    ['speaker' => 'B', 'jp' => '二番線の電車に乗ってください。', 'vi' => 'Hãy lên tàu ở đường ray số 2.'],
                    ['speaker' => 'A', 'jp' => '切符はいくらですか。', 'vi' => 'Vé bao nhiêu tiền?'],
                    ['speaker' => 'B', 'jp' => '五百八十円です。', 'vi' => '580 yên.'],
                ],
                'quiz' => [
                    ['id' => 'q1', 'prompt' => '駅 nghĩa là gì?', 'options' => ['nhà ga', 'khách sạn', 'vé', 'bản đồ'], 'answer' => 'nhà ga', 'explanation' => '駅 là nhà ga.'],
                    ['id' => 'q2', 'prompt' => 'Câu nào dùng để hỏi “Lối ra ở đâu?”', 'options' => ['出口はどこですか。', '切符をください。', '京都へ行きたいです。'], 'answer' => '出口はどこですか。', 'explanation' => '〜はどこですか dùng để hỏi vị trí.'],
                    ['id' => 'q3', 'prompt' => '切符をください。 nghĩa là gì?', 'options' => ['Cho tôi vé.', 'Tôi muốn đi ga.', 'Bản đồ ở đâu?'], 'answer' => 'Cho tôi vé.', 'explanation' => '〜をください là mẫu xin/mua thứ gì đó.'],
                ],
                'mini_task' => [
                    'title' => '5 phút hỏi đường ở ga',
                    'steps' => ['Nghe hội thoại một lần.', 'Đọc to 3 câu có どこですか.', 'Tự nói câu: Tôi muốn đi Kyoto.', 'Làm quiz nhanh.'],
                ],
            ],
            [
                'slug' => 'goi-mon',
                'title' => 'Gọi món',
                'subtitle' => 'Gọi món, hỏi món đề xuất và thanh toán trong nhà hàng.',
                'level' => 'N5',
                'duration_minutes' => 10,
                'tags' => ['nha_hang', 'du_lich', 'doi_song'],
                'goal' => 'Gọi món lịch sự và hỏi món nên thử.',
                'vocabulary' => [
                    ['jp' => 'メニュー', 'reading' => 'メニュー', 'meaning' => 'thực đơn', 'example' => 'メニューをお願いします。'],
                    ['jp' => 'おすすめ', 'reading' => 'おすすめ', 'meaning' => 'món gợi ý', 'example' => 'おすすめは何ですか。'],
                    ['jp' => '水', 'reading' => 'みず', 'meaning' => 'nước', 'example' => '水をください。'],
                    ['jp' => '会計', 'reading' => 'かいけい', 'meaning' => 'thanh toán', 'example' => '会計をお願いします。'],
                    ['jp' => '辛い', 'reading' => 'からい', 'meaning' => 'cay', 'example' => 'これは辛いですか。'],
                ],
                'patterns' => [
                    ['jp' => '〜をお願いします。', 'meaning' => 'Cho tôi ~ / làm ơn ~.', 'usage' => 'Lịch sự hơn khi gọi món.', 'example' => 'ラーメンをお願いします。'],
                    ['jp' => 'おすすめは何ですか。', 'meaning' => 'Món gợi ý là gì?', 'usage' => 'Hỏi món nên thử.', 'example' => '今日のおすすめは何ですか。'],
                    ['jp' => 'これは辛いですか。', 'meaning' => 'Món này có cay không?', 'usage' => 'Hỏi đặc điểm món ăn.', 'example' => 'このカレーは辛いですか。'],
                ],
                'dialogue' => [
                    ['speaker' => 'A', 'jp' => 'すみません。メニューをお願いします。', 'vi' => 'Xin lỗi. Cho tôi thực đơn.'],
                    ['speaker' => 'B', 'jp' => 'はい、どうぞ。', 'vi' => 'Vâng, xin mời.'],
                    ['speaker' => 'A', 'jp' => 'おすすめは何ですか。', 'vi' => 'Món gợi ý là gì?'],
                    ['speaker' => 'B', 'jp' => 'ラーメンがおすすめです。', 'vi' => 'Ramen là món nên thử.'],
                ],
                'quiz' => [
                    ['id' => 'q1', 'prompt' => 'おすすめ là gì?', 'options' => ['món gợi ý', 'nước', 'thanh toán', 'cay'], 'answer' => 'món gợi ý'],
                    ['id' => 'q2', 'prompt' => 'Muốn xin thực đơn, chọn câu nào?', 'options' => ['メニューをお願いします。', '会計をお願いします。', '駅はどこですか。'], 'answer' => 'メニューをお願いします。'],
                    ['id' => 'q3', 'prompt' => '会計をお願いします。 dùng khi nào?', 'options' => ['thanh toán', 'hỏi đường', 'check-in', 'xin lỗi'], 'answer' => 'thanh toán'],
                ],
                'mini_task' => [
                    'title' => '5 phút gọi món không lúng túng',
                    'steps' => ['Nghe 4 câu hội thoại.', 'Đọc to おすすめは何ですか。', 'Lật 5 flashcard món ăn.', 'Làm quiz nhanh.'],
                ],
            ],
            [
                'slug' => 'hoi-duong',
                'title' => 'Hỏi đường',
                'subtitle' => 'Hỏi vị trí, nghe chỉ dẫn rẽ trái/rẽ phải và xác nhận lại.',
                'level' => 'N5',
                'duration_minutes' => 10,
                'tags' => ['du_lich', 'hoi_thoai'],
                'goal' => 'Hỏi đường đơn giản và hiểu chỉ dẫn cơ bản.',
                'vocabulary' => [
                    ['jp' => '右', 'reading' => 'みぎ', 'meaning' => 'bên phải', 'example' => '右へ曲がってください。'],
                    ['jp' => '左', 'reading' => 'ひだり', 'meaning' => 'bên trái', 'example' => '左へ曲がってください。'],
                    ['jp' => 'まっすぐ', 'reading' => 'まっすぐ', 'meaning' => 'đi thẳng', 'example' => 'まっすぐ行ってください。'],
                    ['jp' => '信号', 'reading' => 'しんごう', 'meaning' => 'đèn giao thông', 'example' => '信号を右へ曲がってください。'],
                    ['jp' => '近く', 'reading' => 'ちかく', 'meaning' => 'gần đây', 'example' => '近くにコンビニがありますか。'],
                ],
                'patterns' => [
                    ['jp' => '〜へ行きたいんですが。', 'meaning' => 'Tôi muốn đi tới ~.', 'usage' => 'Mở lời hỏi đường tự nhiên.', 'example' => '駅へ行きたいんですが。'],
                    ['jp' => 'まっすぐ行ってください。', 'meaning' => 'Hãy đi thẳng.', 'usage' => 'Chỉ đường.', 'example' => 'この道をまっすぐ行ってください。'],
                    ['jp' => '〜を右へ曲がってください。', 'meaning' => 'Hãy rẽ phải ở ~.', 'usage' => 'Chỉ điểm rẽ.', 'example' => '信号を右へ曲がってください。'],
                ],
                'dialogue' => [
                    ['speaker' => 'A', 'jp' => 'すみません。駅へ行きたいんですが。', 'vi' => 'Xin lỗi. Tôi muốn đi tới ga.'],
                    ['speaker' => 'B', 'jp' => 'この道をまっすぐ行ってください。', 'vi' => 'Hãy đi thẳng đường này.'],
                    ['speaker' => 'B', 'jp' => '信号を左へ曲がってください。', 'vi' => 'Rẽ trái ở đèn giao thông.'],
                    ['speaker' => 'A', 'jp' => 'ありがとうございます。', 'vi' => 'Cảm ơn.'],
                ],
                'quiz' => [
                    ['id' => 'q1', 'prompt' => 'まっすぐ nghĩa là gì?', 'options' => ['đi thẳng', 'rẽ phải', 'rẽ trái', 'gần đây'], 'answer' => 'đi thẳng'],
                    ['id' => 'q2', 'prompt' => '右へ曲がってください。 nghĩa là gì?', 'options' => ['Hãy rẽ phải.', 'Hãy đi thẳng.', 'Hãy quay lại.'], 'answer' => 'Hãy rẽ phải.'],
                    ['id' => 'q3', 'prompt' => '信号 là gì?', 'options' => ['đèn giao thông', 'nhà ga', 'khách sạn'], 'answer' => 'đèn giao thông'],
                ],
                'mini_task' => [
                    'title' => '5 phút hỏi đường',
                    'steps' => ['Nghe chỉ dẫn một lần.', 'Đọc to 右, 左, まっすぐ.', 'Tự nói đường tới nhà ga.', 'Làm quiz.'],
                ],
            ],
            [
                'slug' => 'check-in-khach-san',
                'title' => 'Check-in khách sạn',
                'subtitle' => 'Đặt phòng, đưa tên, hỏi giờ trả phòng và tiện nghi.',
                'level' => 'N5-N4',
                'duration_minutes' => 12,
                'tags' => ['khach_san', 'du_lich'],
                'goal' => 'Check-in khách sạn bằng câu lịch sự cơ bản.',
                'vocabulary' => [
                    ['jp' => '予約', 'reading' => 'よやく', 'meaning' => 'đặt trước', 'example' => '予約があります。'],
                    ['jp' => '部屋', 'reading' => 'へや', 'meaning' => 'phòng', 'example' => '部屋はどこですか。'],
                    ['jp' => '名前', 'reading' => 'なまえ', 'meaning' => 'tên', 'example' => '名前はタスです。'],
                    ['jp' => 'チェックアウト', 'reading' => 'チェックアウト', 'meaning' => 'trả phòng', 'example' => 'チェックアウトは何時ですか。'],
                    ['jp' => '鍵', 'reading' => 'かぎ', 'meaning' => 'chìa khóa', 'example' => '鍵をお願いします。'],
                ],
                'patterns' => [
                    ['jp' => '予約があります。', 'meaning' => 'Tôi có đặt phòng.', 'usage' => 'Nói khi tới quầy lễ tân.', 'example' => '田中の名前で予約があります。'],
                    ['jp' => '〜は何時ですか。', 'meaning' => '~ lúc mấy giờ?', 'usage' => 'Hỏi thời gian.', 'example' => 'チェックアウトは何時ですか。'],
                    ['jp' => '〜を見せてください。', 'meaning' => 'Vui lòng cho tôi xem ~.', 'usage' => 'Lễ tân có thể dùng để xin giấy tờ.', 'example' => 'パスポートを見せてください。'],
                ],
                'dialogue' => [
                    ['speaker' => 'A', 'jp' => 'こんばんは。予約があります。', 'vi' => 'Chào buổi tối. Tôi có đặt phòng.'],
                    ['speaker' => 'B', 'jp' => 'お名前をお願いします。', 'vi' => 'Xin cho biết tên của quý khách.'],
                    ['speaker' => 'A', 'jp' => 'グエンです。', 'vi' => 'Tôi là Nguyễn.'],
                    ['speaker' => 'B', 'jp' => 'チェックアウトは十時です。', 'vi' => 'Giờ trả phòng là 10 giờ.'],
                ],
                'quiz' => [
                    ['id' => 'q1', 'prompt' => '予約があります。 nghĩa là gì?', 'options' => ['Tôi có đặt phòng.', 'Tôi muốn gọi món.', 'Tôi muốn hỏi đường.'], 'answer' => 'Tôi có đặt phòng.'],
                    ['id' => 'q2', 'prompt' => 'チェックアウトは何時ですか。 dùng để hỏi gì?', 'options' => ['giờ trả phòng', 'giá phòng', 'số phòng'], 'answer' => 'giờ trả phòng'],
                    ['id' => 'q3', 'prompt' => '鍵 nghĩa là gì?', 'options' => ['chìa khóa', 'hộ chiếu', 'thang máy'], 'answer' => 'chìa khóa'],
                ],
                'mini_task' => [
                    'title' => '5 phút check-in',
                    'steps' => ['Đọc to 予約があります。', 'Nghe hội thoại lễ tân.', 'Lật flashcard khách sạn.', 'Tự hỏi giờ trả phòng.'],
                ],
            ],
            [
                'slug' => 'phong-van-viec-lam',
                'title' => 'Phỏng vấn việc làm',
                'subtitle' => 'Tự giới thiệu, nói kinh nghiệm và trả lời lý do ứng tuyển.',
                'level' => 'N4-N3',
                'duration_minutes' => 15,
                'tags' => ['cong_viec'],
                'goal' => 'Trả lời ngắn gọn trong phỏng vấn bằng tiếng Nhật lịch sự.',
                'vocabulary' => [
                    ['jp' => '面接', 'reading' => 'めんせつ', 'meaning' => 'phỏng vấn', 'example' => '明日、面接があります。'],
                    ['jp' => '経験', 'reading' => 'けいけん', 'meaning' => 'kinh nghiệm', 'example' => '経験があります。'],
                    ['jp' => '志望動機', 'reading' => 'しぼうどうき', 'meaning' => 'lý do ứng tuyển', 'example' => '志望動機を教えてください。'],
                    ['jp' => '長所', 'reading' => 'ちょうしょ', 'meaning' => 'điểm mạnh', 'example' => '私の長所は責任感です。'],
                    ['jp' => '短所', 'reading' => 'たんしょ', 'meaning' => 'điểm yếu', 'example' => '短所は少し心配しすぎることです。'],
                ],
                'patterns' => [
                    ['jp' => '〜と申します。', 'meaning' => 'Tôi tên là ~.', 'usage' => 'Tự giới thiệu lịch sự.', 'example' => 'グエンと申します。'],
                    ['jp' => '〜の経験があります。', 'meaning' => 'Tôi có kinh nghiệm về ~.', 'usage' => 'Nói kinh nghiệm.', 'example' => '接客の経験があります。'],
                    ['jp' => '〜に興味があります。', 'meaning' => 'Tôi quan tâm tới ~.', 'usage' => 'Nói lý do ứng tuyển.', 'example' => '日本のサービスに興味があります。'],
                ],
                'dialogue' => [
                    ['speaker' => 'A', 'jp' => '自己紹介をお願いします。', 'vi' => 'Vui lòng tự giới thiệu.'],
                    ['speaker' => 'B', 'jp' => 'グエンと申します。よろしくお願いいたします。', 'vi' => 'Tôi tên là Nguyễn. Rất mong được giúp đỡ.'],
                    ['speaker' => 'A', 'jp' => 'どんな経験がありますか。', 'vi' => 'Bạn có kinh nghiệm gì?'],
                    ['speaker' => 'B', 'jp' => '接客の経験が二年あります。', 'vi' => 'Tôi có 2 năm kinh nghiệm phục vụ khách hàng.'],
                ],
                'quiz' => [
                    ['id' => 'q1', 'prompt' => '面接 nghĩa là gì?', 'options' => ['phỏng vấn', 'cuộc họp', 'email', 'hợp đồng'], 'answer' => 'phỏng vấn'],
                    ['id' => 'q2', 'prompt' => 'Câu tự giới thiệu lịch sự là câu nào?', 'options' => ['グエンと申します。', '水をください。', '駅はどこですか。'], 'answer' => 'グエンと申します。'],
                    ['id' => 'q3', 'prompt' => '経験があります nghĩa là gì?', 'options' => ['có kinh nghiệm', 'có đặt phòng', 'có bản đồ'], 'answer' => 'có kinh nghiệm'],
                ],
                'mini_task' => [
                    'title' => '5 phút trả lời phỏng vấn',
                    'steps' => ['Đọc to phần tự giới thiệu.', 'Ghi 1 kinh nghiệm của bạn bằng tiếng Nhật.', 'Nghe hội thoại.', 'Làm quiz.'],
                ],
            ],
            [
                'slug' => 'email-cong-viec',
                'title' => 'Email công việc',
                'subtitle' => 'Mở đầu email, nhờ xác nhận, gửi tài liệu và cảm ơn.',
                'level' => 'N4-N3',
                'duration_minutes' => 15,
                'tags' => ['email', 'cong_viec'],
                'goal' => 'Viết email công việc ngắn, lịch sự và rõ ý.',
                'vocabulary' => [
                    ['jp' => 'お世話になっております', 'reading' => 'おせわになっております', 'meaning' => 'câu chào email lịch sự', 'example' => 'いつもお世話になっております。'],
                    ['jp' => '確認', 'reading' => 'かくにん', 'meaning' => 'xác nhận', 'example' => 'ご確認ください。'],
                    ['jp' => '資料', 'reading' => 'しりょう', 'meaning' => 'tài liệu', 'example' => '資料を送ります。'],
                    ['jp' => '添付', 'reading' => 'てんぷ', 'meaning' => 'đính kèm', 'example' => '資料を添付します。'],
                    ['jp' => 'よろしくお願いいたします', 'reading' => 'よろしくおねがいいたします', 'meaning' => 'mong anh/chị hỗ trợ', 'example' => 'よろしくお願いいたします。'],
                ],
                'patterns' => [
                    ['jp' => '〜をご確認ください。', 'meaning' => 'Vui lòng xác nhận/xem ~.', 'usage' => 'Nhờ đối phương kiểm tra.', 'example' => '資料をご確認ください。'],
                    ['jp' => '〜を添付いたします。', 'meaning' => 'Tôi xin đính kèm ~.', 'usage' => 'Dùng trong email lịch sự.', 'example' => '見積書を添付いたします。'],
                    ['jp' => '何卒よろしくお願いいたします。', 'meaning' => 'Rất mong anh/chị hỗ trợ.', 'usage' => 'Kết email trang trọng.', 'example' => 'ご確認のほど、何卒よろしくお願いいたします。'],
                ],
                'dialogue' => [
                    ['speaker' => 'Email', 'jp' => 'いつもお世話になっております。', 'vi' => 'Cảm ơn anh/chị luôn hỗ trợ.'],
                    ['speaker' => 'Email', 'jp' => '資料を添付いたします。', 'vi' => 'Tôi xin đính kèm tài liệu.'],
                    ['speaker' => 'Email', 'jp' => 'ご確認のほど、よろしくお願いいたします。', 'vi' => 'Mong anh/chị xác nhận giúp.'],
                ],
                'quiz' => [
                    ['id' => 'q1', 'prompt' => '資料 nghĩa là gì?', 'options' => ['tài liệu', 'cuộc họp', 'vé', 'đặt phòng'], 'answer' => 'tài liệu'],
                    ['id' => 'q2', 'prompt' => '〜を添付いたします。 nghĩa là gì?', 'options' => ['Tôi xin đính kèm ~.', 'Vui lòng gọi món.', 'Tôi muốn đi ~.'], 'answer' => 'Tôi xin đính kèm ~.'],
                    ['id' => 'q3', 'prompt' => 'ご確認ください。 dùng để làm gì?', 'options' => ['nhờ xác nhận', 'xin lỗi', 'đặt vé'], 'answer' => 'nhờ xác nhận'],
                ],
                'mini_task' => [
                    'title' => '5 phút viết email',
                    'steps' => ['Đọc to 3 câu email mẫu.', 'Thay 資料 bằng từ bạn cần gửi.', 'Viết 1 email 3 dòng.', 'Làm quiz.'],
                ],
            ],
            [
                'slug' => 'giao-tiep-cong-so',
                'title' => 'Giao tiếp công sở',
                'subtitle' => 'Nhờ hỗ trợ, xác nhận lịch họp và báo tiến độ.',
                'level' => 'N4',
                'duration_minutes' => 12,
                'tags' => ['cong_so', 'cong_viec', 'hoi_thoai'],
                'goal' => 'Nói chuyện ngắn trong môi trường công sở.',
                'vocabulary' => [
                    ['jp' => '会議', 'reading' => 'かいぎ', 'meaning' => 'cuộc họp', 'example' => '会議は三時からです。'],
                    ['jp' => '予定', 'reading' => 'よてい', 'meaning' => 'lịch trình', 'example' => '今日の予定を確認します。'],
                    ['jp' => '進捗', 'reading' => 'しんちょく', 'meaning' => 'tiến độ', 'example' => '進捗を報告します。'],
                    ['jp' => '相談', 'reading' => 'そうだん', 'meaning' => 'trao đổi/tư vấn', 'example' => '少し相談してもいいですか。'],
                    ['jp' => '依頼', 'reading' => 'いらい', 'meaning' => 'yêu cầu/nhờ việc', 'example' => '依頼があります。'],
                ],
                'patterns' => [
                    ['jp' => '少し相談してもいいですか。', 'meaning' => 'Tôi trao đổi một chút được không?', 'usage' => 'Mở lời xin trao đổi.', 'example' => 'この件について少し相談してもいいですか。'],
                    ['jp' => '〜までにできます。', 'meaning' => 'Tôi có thể làm xong trước ~.', 'usage' => 'Báo deadline.', 'example' => '金曜日までにできます。'],
                    ['jp' => '確認してから連絡します。', 'meaning' => 'Tôi sẽ xác nhận rồi liên lạc.', 'usage' => 'Khi chưa chắc thông tin.', 'example' => '予定を確認してから連絡します。'],
                ],
                'dialogue' => [
                    ['speaker' => 'A', 'jp' => 'この件について少し相談してもいいですか。', 'vi' => 'Tôi trao đổi một chút về việc này được không?'],
                    ['speaker' => 'B', 'jp' => 'はい、大丈夫です。', 'vi' => 'Vâng, được.'],
                    ['speaker' => 'A', 'jp' => '金曜日までにできます。', 'vi' => 'Tôi có thể làm xong trước thứ Sáu.'],
                    ['speaker' => 'B', 'jp' => 'ありがとうございます。', 'vi' => 'Cảm ơn.'],
                ],
                'quiz' => [
                    ['id' => 'q1', 'prompt' => '会議 nghĩa là gì?', 'options' => ['cuộc họp', 'nhà hàng', 'hộ chiếu'], 'answer' => 'cuộc họp'],
                    ['id' => 'q2', 'prompt' => '少し相談してもいいですか。 nghĩa là gì?', 'options' => ['Tôi trao đổi một chút được không?', 'Cho tôi nước.', 'Ga ở đâu?'], 'answer' => 'Tôi trao đổi một chút được không?'],
                    ['id' => 'q3', 'prompt' => '進捗 là gì?', 'options' => ['tiến độ', 'lịch trình', 'tài liệu'], 'answer' => 'tiến độ'],
                ],
                'mini_task' => [
                    'title' => '5 phút nói ở công sở',
                    'steps' => ['Nghe hội thoại.', 'Đọc to câu xin trao đổi.', 'Tự nói một deadline.', 'Làm quiz.'],
                ],
            ],
            [
                'slug' => 'anime-phrase',
                'title' => 'Anime phrase',
                'subtitle' => 'Cụm câu hay gặp trong anime nhưng vẫn dùng được trong đời sống nếu đúng ngữ cảnh.',
                'level' => 'N5-N4',
                'duration_minutes' => 10,
                'tags' => ['anime', 'doi_song'],
                'goal' => 'Hiểu nhanh các câu ngắn quen tai.',
                'vocabulary' => [
                    ['jp' => '大丈夫', 'reading' => 'だいじょうぶ', 'meaning' => 'ổn/không sao', 'example' => '大丈夫ですか。'],
                    ['jp' => '本当', 'reading' => 'ほんとう', 'meaning' => 'thật không', 'example' => '本当ですか。'],
                    ['jp' => 'すごい', 'reading' => 'すごい', 'meaning' => 'tuyệt/quá giỏi', 'example' => 'すごいですね。'],
                    ['jp' => '約束', 'reading' => 'やくそく', 'meaning' => 'lời hứa', 'example' => '約束します。'],
                    ['jp' => '待って', 'reading' => 'まって', 'meaning' => 'chờ đã', 'example' => 'ちょっと待って。'],
                ],
                'patterns' => [
                    ['jp' => '〜てもいいですか。', 'meaning' => 'Tôi có thể ~ được không?', 'usage' => 'Xin phép.', 'example' => '聞いてもいいですか。'],
                    ['jp' => 'ちょっと待ってください。', 'meaning' => 'Vui lòng chờ một chút.', 'usage' => 'Lịch sự hơn 待って.', 'example' => 'ちょっと待ってください。'],
                    ['jp' => '本当ですか。', 'meaning' => 'Thật không?', 'usage' => 'Hỏi lại khi ngạc nhiên.', 'example' => 'え、本当ですか。'],
                ],
                'dialogue' => [
                    ['speaker' => 'A', 'jp' => '大丈夫ですか。', 'vi' => 'Bạn ổn chứ?'],
                    ['speaker' => 'B', 'jp' => 'はい、大丈夫です。', 'vi' => 'Vâng, tôi ổn.'],
                    ['speaker' => 'A', 'jp' => '本当ですか。', 'vi' => 'Thật không?'],
                    ['speaker' => 'B', 'jp' => '本当です。', 'vi' => 'Thật.'],
                ],
                'quiz' => [
                    ['id' => 'q1', 'prompt' => '大丈夫ですか。 nghĩa là gì?', 'options' => ['Bạn ổn chứ?', 'Bạn đi đâu?', 'Bạn ăn chưa?'], 'answer' => 'Bạn ổn chứ?'],
                    ['id' => 'q2', 'prompt' => '待って nghĩa là gì?', 'options' => ['chờ đã', 'đừng đi', 'xin lỗi'], 'answer' => 'chờ đã'],
                    ['id' => 'q3', 'prompt' => 'すごいですね。 dùng khi nào?', 'options' => ['khen/ngạc nhiên', 'check-in', 'thanh toán'], 'answer' => 'khen/ngạc nhiên'],
                ],
                'mini_task' => [
                    'title' => '5 phút bắt câu quen',
                    'steps' => ['Nghe 5 cụm câu.', 'Đọc to 大丈夫ですか。', 'Lật flashcard.', 'Làm quiz.'],
                ],
            ],
            [
                'slug' => 'du-hoc',
                'title' => 'Du học',
                'subtitle' => 'Nói chuyện ở lớp, ký túc xá và văn phòng trường.',
                'level' => 'N5-N4',
                'duration_minutes' => 12,
                'tags' => ['du_hoc', 'doi_song'],
                'goal' => 'Xử lý hội thoại cơ bản khi mới đi học ở Nhật.',
                'vocabulary' => [
                    ['jp' => '授業', 'reading' => 'じゅぎょう', 'meaning' => 'giờ học', 'example' => '授業は九時からです。'],
                    ['jp' => '寮', 'reading' => 'りょう', 'meaning' => 'ký túc xá', 'example' => '寮に住んでいます。'],
                    ['jp' => '学生証', 'reading' => 'がくせいしょう', 'meaning' => 'thẻ sinh viên', 'example' => '学生証を見せてください。'],
                    ['jp' => '宿題', 'reading' => 'しゅくだい', 'meaning' => 'bài tập về nhà', 'example' => '宿題があります。'],
                    ['jp' => '事務室', 'reading' => 'じむしつ', 'meaning' => 'văn phòng trường', 'example' => '事務室はどこですか。'],
                ],
                'patterns' => [
                    ['jp' => '〜に住んでいます。', 'meaning' => 'Tôi đang sống ở ~.', 'usage' => 'Nói nơi ở.', 'example' => '寮に住んでいます。'],
                    ['jp' => '〜は何時からですか。', 'meaning' => '~ bắt đầu từ mấy giờ?', 'usage' => 'Hỏi thời gian bắt đầu.', 'example' => '授業は何時からですか。'],
                    ['jp' => 'もう一度お願いします。', 'meaning' => 'Vui lòng nói lại một lần nữa.', 'usage' => 'Khi chưa nghe rõ.', 'example' => 'すみません、もう一度お願いします。'],
                ],
                'dialogue' => [
                    ['speaker' => 'A', 'jp' => '授業は何時からですか。', 'vi' => 'Giờ học bắt đầu lúc mấy giờ?'],
                    ['speaker' => 'B', 'jp' => '九時からです。', 'vi' => 'Từ 9 giờ.'],
                    ['speaker' => 'A', 'jp' => 'すみません、もう一度お願いします。', 'vi' => 'Xin lỗi, vui lòng nói lại một lần nữa.'],
                    ['speaker' => 'B', 'jp' => '九時からです。', 'vi' => 'Từ 9 giờ.'],
                ],
                'quiz' => [
                    ['id' => 'q1', 'prompt' => '授業 nghĩa là gì?', 'options' => ['giờ học', 'ký túc xá', 'bài tập'], 'answer' => 'giờ học'],
                    ['id' => 'q2', 'prompt' => '寮に住んでいます。 nghĩa là gì?', 'options' => ['Tôi sống ở ký túc xá.', 'Tôi có giờ học.', 'Tôi đi nhà ga.'], 'answer' => 'Tôi sống ở ký túc xá.'],
                    ['id' => 'q3', 'prompt' => 'もう一度お願いします。 dùng khi nào?', 'options' => ['khi muốn nghe lại', 'khi trả phòng', 'khi gọi món'], 'answer' => 'khi muốn nghe lại'],
                ],
                'mini_task' => [
                    'title' => '5 phút ở trường',
                    'steps' => ['Nghe hội thoại hỏi giờ học.', 'Đọc to câu xin nhắc lại.', 'Lật flashcard du học.', 'Làm quiz.'],
                ],
            ],
            [
                'slug' => 'hoi-thoai-doi-song',
                'title' => 'Hội thoại đời sống',
                'subtitle' => 'Chào hỏi, hỏi thăm, rủ đi chơi và phản hồi tự nhiên.',
                'level' => 'N5',
                'duration_minutes' => 10,
                'tags' => ['doi_song', 'hoi_thoai'],
                'goal' => 'Nói vài câu tự nhiên trong đời sống hằng ngày.',
                'vocabulary' => [
                    ['jp' => '久しぶり', 'reading' => 'ひさしぶり', 'meaning' => 'lâu rồi không gặp', 'example' => '久しぶりですね。'],
                    ['jp' => '元気', 'reading' => 'げんき', 'meaning' => 'khỏe', 'example' => '元気ですか。'],
                    ['jp' => '一緒に', 'reading' => 'いっしょに', 'meaning' => 'cùng nhau', 'example' => '一緒に行きませんか。'],
                    ['jp' => '週末', 'reading' => 'しゅうまつ', 'meaning' => 'cuối tuần', 'example' => '週末は何をしますか。'],
                    ['jp' => '楽しい', 'reading' => 'たのしい', 'meaning' => 'vui', 'example' => '楽しかったです。'],
                ],
                'patterns' => [
                    ['jp' => '〜ませんか。', 'meaning' => 'Cùng ~ không?', 'usage' => 'Rủ ai đó làm gì.', 'example' => '一緒に行きませんか。'],
                    ['jp' => '〜はどうですか。', 'meaning' => '~ thì sao?', 'usage' => 'Đề xuất/ hỏi ý kiến.', 'example' => '日曜日はどうですか。'],
                    ['jp' => '楽しかったです。', 'meaning' => 'Đã rất vui.', 'usage' => 'Nói cảm nhận sau hoạt động.', 'example' => '今日は楽しかったです。'],
                ],
                'dialogue' => [
                    ['speaker' => 'A', 'jp' => '久しぶりですね。元気ですか。', 'vi' => 'Lâu rồi không gặp. Bạn khỏe không?'],
                    ['speaker' => 'B', 'jp' => 'はい、元気です。', 'vi' => 'Vâng, tôi khỏe.'],
                    ['speaker' => 'A', 'jp' => '週末、一緒に映画を見ませんか。', 'vi' => 'Cuối tuần cùng xem phim không?'],
                    ['speaker' => 'B', 'jp' => 'いいですね。', 'vi' => 'Hay đấy.'],
                ],
                'quiz' => [
                    ['id' => 'q1', 'prompt' => '久しぶりですね。 nghĩa là gì?', 'options' => ['Lâu rồi không gặp.', 'Bạn ổn chứ?', 'Xin thanh toán.'], 'answer' => 'Lâu rồi không gặp.'],
                    ['id' => 'q2', 'prompt' => '一緒に行きませんか。 dùng để làm gì?', 'options' => ['rủ đi cùng', 'hỏi đường', 'xin lỗi'], 'answer' => 'rủ đi cùng'],
                    ['id' => 'q3', 'prompt' => '楽しかったです。 nghĩa là gì?', 'options' => ['Đã rất vui.', 'Rất cay.', 'Tôi có đặt phòng.'], 'answer' => 'Đã rất vui.'],
                ],
                'mini_task' => [
                    'title' => '5 phút nói chuyện đời sống',
                    'steps' => ['Nghe hội thoại.', 'Đọc to lời rủ đi chơi.', 'Tự thay 映画 bằng hoạt động khác.', 'Làm quiz.'],
                ],
            ],
        ];
    }
}
