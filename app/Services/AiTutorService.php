<?php

namespace App\Services;

use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class AiTutorService
{
    public const EXPLAIN_GRAMMAR = 'explain_grammar';
    public const MORE_EXAMPLES = 'more_examples';
    public const CHECK_TRANSLATION = 'check_translation';
    public const CORRECT_SENTENCE = 'correct_sentence';
    public const MEMORY_HINT = 'memory_hint';
    public const MINI_QUIZ = 'mini_quiz';
    public const SUMMARIZE_LESSON = 'summarize_lesson';
    public const REVIEW_MISTAKES = 'review_mistakes';

    private const VOCAB_KEYS = ['vocab', 'mau_cau', 'countries', 'proper_nouns', 'cau', 'places', 'rail'];

    public function __construct(private UserMistakeService $mistakeService) {}

    public static function actions(): array
    {
        return array_keys(self::actionLabels());
    }

    public static function actionLabels(): array
    {
        return [
            self::EXPLAIN_GRAMMAR => 'Giải thích ngữ pháp',
            self::MORE_EXAMPLES => 'Tạo thêm ví dụ',
            self::CHECK_TRANSLATION => 'Kiểm tra dịch Việt -> Nhật',
            self::CORRECT_SENTENCE => 'Sửa câu tự viết',
            self::MEMORY_HINT => 'Gợi ý nhớ từ',
            self::MINI_QUIZ => 'Tạo mini quiz',
            self::SUMMARIZE_LESSON => 'Tóm tắt bài',
            self::REVIEW_MISTAKES => 'Gợi ý ôn lỗi sai',
        ];
    }

    public function answer(User $user, MinnaLesson $lesson, array $input): array
    {
        $lesson->loadMissing('sections');
        $action = (string) $input['action'];
        $context = $this->buildContext($user, $lesson, $input);
        $provider = strtolower((string) config('ai_tutor.provider', 'local'));

        if ($provider === 'openai' && (string) config('ai_tutor.openai.api_key') !== '') {
            $remote = $this->answerWithOpenAi($action, $context);
            if ($remote) {
                return $remote;
            }
        }

        return $this->fallbackAnswer($action, $context);
    }

    private function answerWithOpenAi(string $action, array $context): ?array
    {
        try {
            $response = Http::timeout((int) config('ai_tutor.openai.timeout', 20))
                ->withToken((string) config('ai_tutor.openai.api_key'))
                ->asJson()
                ->post((string) config('ai_tutor.openai.endpoint'), [
                    'model' => (string) config('ai_tutor.openai.model'),
                    'input' => [
                        [
                            'role' => 'system',
                            'content' => $this->systemPrompt(),
                        ],
                        [
                            'role' => 'user',
                            'content' => json_encode([
                                'task' => self::actionLabels()[$action] ?? $action,
                                'action' => $action,
                                'context' => $context,
                                'required_output' => [
                                    'title' => 'string',
                                    'answer' => 'string tiếng Việt, ngắn gọn',
                                    'bullets' => 'array string',
                                    'examples' => 'array {jp, vi}',
                                    'quiz' => 'array {prompt, answer, options?}',
                                ],
                            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                        ],
                    ],
                    'temperature' => 0.25,
                    'max_output_tokens' => 900,
                ]);

            if (! $response->ok()) {
                return null;
            }

            $text = $response->json('output_text') ?: $this->extractOpenAiText($response->json());
            $text = trim((string) $text);
            if ($text === '') {
                return null;
            }

            return [
                'action' => $action,
                'title' => self::actionLabels()[$action] ?? 'Trợ lý học tiếng Nhật',
                'answer' => $text,
                'bullets' => [],
                'examples' => [],
                'quiz' => [],
                'provider' => 'openai',
                'model' => (string) config('ai_tutor.openai.model'),
                'context_summary' => $this->contextSummary($context),
            ];
        } catch (Throwable) {
            return null;
        }
    }

    private function fallbackAnswer(string $action, array $context): array
    {
        return match ($action) {
            self::EXPLAIN_GRAMMAR => $this->explainGrammar($context),
            self::MORE_EXAMPLES => $this->moreExamples($context),
            self::CHECK_TRANSLATION => $this->checkTranslation($context),
            self::CORRECT_SENTENCE => $this->correctSentence($context),
            self::MEMORY_HINT => $this->memoryHint($context),
            self::MINI_QUIZ => $this->miniQuiz($context),
            self::SUMMARIZE_LESSON => $this->summarizeLesson($context),
            self::REVIEW_MISTAKES => $this->reviewMistakes($context),
            default => $this->baseResponse($action, 'Trợ lý học tiếng Nhật', 'Mình chưa hỗ trợ thao tác này.', $context),
        };
    }

    private function explainGrammar(array $context): array
    {
        $grammar = $this->pickGrammar($context);
        $title = $grammar['title'] ?? 'Ngữ pháp trong bài';
        $pattern = $grammar['pattern'] ?? $title;

        return $this->baseResponse(self::EXPLAIN_GRAMMAR, 'Giải thích: '.$title, 'Điểm này nên học theo 3 lớp: mẫu câu, ý nghĩa, rồi ví dụ tự thay từ. Với trình độ '.$context['user']['level'].', hãy ưu tiên hiểu cách dùng trước khi học thuộc dài.', $context, [
            'Mẫu chính: '.$pattern,
            'Ý nghĩa: '.($grammar['note'] ?? 'dùng để diễn đạt ý trong bài hiện tại.'),
            'Khi luyện, hãy đổi chủ ngữ hoặc từ vựng trong bài để tạo câu mới.',
        ], $this->examplesFromContext($context, 2));
    }

    private function moreExamples(array $context): array
    {
        $examples = $this->examplesFromContext($context, 4);

        return $this->baseResponse(self::MORE_EXAMPLES, 'Ví dụ theo trình độ '.$context['user']['level'], 'Các ví dụ dưới đây chỉ dùng từ và mẫu gần với bài hiện tại để không nhảy quá xa khỏi lộ trình.', $context, [
            'Đọc ví dụ tiếng Nhật trước.',
            'Tự đoán nghĩa rồi mới xem tiếng Việt.',
            'Thay một từ trong câu để tạo câu của riêng bạn.',
        ], $examples);
    }

    private function checkTranslation(array $context): array
    {
        $prompt = trim((string) $context['request']['prompt']);
        $selected = trim((string) $context['request']['selected_text']);
        $vocab = collect($context['lesson']['vocabulary']);
        $matched = $vocab->filter(fn (array $item) => $prompt !== '' && Str::contains(Str::lower($prompt), Str::lower($item['meaning'])))->take(5)->values();

        $bullets = $matched->map(fn (array $item) => $item['meaning'].' -> '.$item['japanese'])->all();
        if ($bullets === []) {
            $bullets = ['Chưa tìm thấy từ khóa khớp rõ trong bài. Hãy nhập câu tiếng Việt cụ thể hơn hoặc chọn một câu mẫu trong bài.'];
        }

        return $this->baseResponse(self::CHECK_TRANSLATION, 'Kiểm tra câu dịch Việt -> Nhật', $selected !== ''
            ? 'Câu mẫu/đáp án bạn đang đối chiếu: '.$selected
            : 'Bản local đang kiểm tra theo từ vựng trong bài. Nếu bật provider AI, hệ thống sẽ chấm tự nhiên hơn.', $context, $bullets);
    }

    private function correctSentence(array $context): array
    {
        $prompt = trim((string) $context['request']['prompt']);
        $clean = preg_replace('/\s+/u', ' ', $prompt) ?: $prompt;
        $hasJapanese = (bool) preg_match('/[\p{Hiragana}\p{Katakana}\p{Han}]/u', $prompt);

        $bullets = $hasJapanese
            ? [
                'Câu đã chuẩn hóa khoảng trắng: '.$clean,
                'Kiểm tra lại trợ từ は, が, を, に, で theo động từ chính.',
                'Nếu câu có thời gian/địa điểm, đặt theo thứ tự: thời gian -> nơi chốn -> tân ngữ -> động từ.',
            ]
            : [
                'Bạn hãy nhập câu tiếng Nhật cần sửa.',
                'Có thể viết bằng hiragana/kanji, hệ thống sẽ dùng ngữ cảnh bài để góp ý.',
            ];

        return $this->baseResponse(self::CORRECT_SENTENCE, 'Sửa câu tự viết', 'Mình giữ góp ý trong phạm vi bài hiện tại để tránh sửa quá nâng cao so với lộ trình của bạn.', $context, $bullets);
    }

    private function memoryHint(array $context): array
    {
        $target = trim((string) $context['request']['selected_text']);
        $item = collect($context['lesson']['vocabulary'])->first(function (array $row) use ($target) {
            return $target !== '' && (Str::contains($row['japanese'], $target) || Str::contains($target, $row['japanese']));
        }) ?: collect($context['lesson']['vocabulary'])->first();

        if (! $item) {
            return $this->baseResponse(self::MEMORY_HINT, 'Gợi ý nhớ từ', 'Bài này chưa có từ vựng đủ rõ để tạo mẹo nhớ.', $context);
        }

        $bullets = [
            'Từ cần nhớ: '.$item['japanese'].' = '.$item['meaning'],
            'Đọc to 3 lần rồi che nghĩa tiếng Việt.',
            'Tự đặt một câu ngắn có từ này trong tình huống '.$this->learningReasonLabel($context).'.',
        ];
        if (($item['kanji'] ?? '') !== '') {
            $bullets[] = 'Nếu có kanji, nhớ theo cụm Hán tự: '.$item['kanji'].'.';
        }

        return $this->baseResponse(self::MEMORY_HINT, 'Cách nhớ: '.$item['japanese'], 'Mẹo nhớ tốt nhất là gắn từ vào một tình huống cá nhân, không chỉ đọc danh sách.', $context, $bullets);
    }

    private function miniQuiz(array $context): array
    {
        $items = collect($context['lesson']['vocabulary'])->take(5)->values();
        $answers = $items->pluck('meaning')->unique()->values()->all();
        $quiz = $items->map(function (array $item, int $index) use ($answers) {
            $options = [$item['meaning']];
            $cursor = $index + 1;
            while (count($options) < 4 && count($options) < count($answers)) {
                $candidate = $answers[$cursor % max(count($answers), 1)];
                if (! in_array($candidate, $options, true)) {
                    $options[] = $candidate;
                }
                $cursor++;
            }
            sort($options);

            return [
                'prompt' => $item['japanese'],
                'answer' => $item['meaning'],
                'options' => $options,
            ];
        })->all();

        return $this->baseResponse(self::MINI_QUIZ, 'Mini quiz từ bài '.$context['lesson']['number'], 'Làm nhanh các câu này trước khi rời bài để khóa từ vựng vào trí nhớ.', $context, [], [], $quiz);
    }

    private function summarizeLesson(array $context): array
    {
        $lesson = $context['lesson'];
        $bullets = [
            'Bài '.$lesson['number'].': '.$lesson['title'],
            'Từ/câu học chính: '.count($lesson['vocabulary']).' mục đang được lấy làm ngữ cảnh.',
            'Ngữ pháp chính: '.max(1, count($lesson['grammar'])).' điểm cần xem.',
            'Mục tiêu cá nhân: '.$context['user']['goal'],
        ];

        return $this->baseResponse(self::SUMMARIZE_LESSON, 'Tóm tắt bài '.$lesson['number'], 'Bài này nên học theo thứ tự: từ vựng -> ngữ pháp -> ví dụ -> quiz ngắn -> ôn lỗi sai.', $context, $bullets);
    }

    private function reviewMistakes(array $context): array
    {
        $mistakes = $context['mistakes'];
        $steps = collect($mistakes['review_plan']['steps'] ?? [])->take(4)->map(fn (array $step) => ($step['title'] ?? 'Ôn lại').' - '.($step['detail'] ?? ''))->all();

        if ($steps === []) {
            $steps = ['Làm một mini quiz trong bài hiện tại.', 'Ôn 5 flashcard gần nhất.', 'Ghi lại một câu tự viết để AI sửa.'];
        }

        return $this->baseResponse(self::REVIEW_MISTAKES, 'Lộ trình sửa lỗi 5 phút', 'Dựa trên quiz, flashcard và bài điểm thấp gần đây, đây là vòng ôn ngắn nên làm ngay.', $context, $steps);
    }

    private function buildContext(User $user, MinnaLesson $lesson, array $input): array
    {
        $max = max(3, (int) config('ai_tutor.max_context_items', 8));
        $mistakes = $this->mistakeService->build($user);

        return [
            'request' => [
                'action' => (string) ($input['action'] ?? ''),
                'prompt' => Str::limit(trim((string) ($input['prompt'] ?? '')), 1600, ''),
                'selected_text' => Str::limit(trim((string) ($input['selected_text'] ?? '')), 800, ''),
                'section_key' => trim((string) ($input['section_key'] ?? '')),
            ],
            'user' => [
                'level' => $user->placement_test_level ?: $user->onboarding_level ?: 'chưa rõ',
                'goal' => $user->jlpt_goal ?: 'chưa chọn JLPT',
                'daily_minutes' => (int) ($user->daily_study_minutes ?? 20),
                'learning_reasons' => array_values(array_filter((array) ($user->learning_reasons ?? []))),
            ],
            'lesson' => [
                'id' => $lesson->id,
                'number' => $lesson->number,
                'title' => $lesson->title,
                'description' => $lesson->description,
                'vocabulary' => $this->extractVocabulary($lesson)->take($max)->values()->all(),
                'grammar' => $this->extractGrammar($lesson)->take($max)->values()->all(),
                'sections' => $lesson->sections->map(fn (MinnaSection $section) => [
                    'key' => $section->key,
                    'title' => $section->title,
                ])->values()->all(),
            ],
            'mistakes' => [
                'summary' => $mistakes['summary'] ?? [],
                'weak_vocabulary' => collect($mistakes['weak_vocabulary'] ?? [])->take(5)->values()->all(),
                'weak_grammar' => collect($mistakes['weak_grammar'] ?? [])->take(5)->values()->all(),
                'review_plan' => $mistakes['review_plan'] ?? [],
            ],
        ];
    }

    private function extractVocabulary(MinnaLesson $lesson): Collection
    {
        return $lesson->sections
            ->where('key', 'tu-vung')
            ->flatMap(function (MinnaSection $section) {
                $content = is_array($section->content) ? $section->content : [];
                $rows = [];

                foreach (self::VOCAB_KEYS as $key) {
                    foreach (($content[$key] ?? []) as $item) {
                        if (! is_array($item)) {
                            continue;
                        }

                        $japanese = trim((string) ($item['tu_vung'] ?? $item['jp'] ?? ''));
                        $meaning = trim((string) ($item['nghia'] ?? ''));
                        if ($japanese === '' || $meaning === '') {
                            continue;
                        }

                        $rows[] = [
                            'japanese' => $japanese,
                            'meaning' => $meaning,
                            'reading' => trim((string) ($item['romaji'] ?? $item['phat_am'] ?? '')),
                            'kanji' => trim((string) ($item['han_tu'] ?? '')),
                            'group' => $key,
                        ];
                    }
                }

                return $rows;
            })
            ->unique(fn (array $item) => $item['japanese'].'|'.$item['meaning'])
            ->values();
    }

    private function extractGrammar(MinnaLesson $lesson): Collection
    {
        return $lesson->sections
            ->where('key', 'ngu-phap')
            ->flatMap(function (MinnaSection $section) {
                $content = is_array($section->content) ? $section->content : [];
                $rows = [];

                foreach ($content as $item) {
                    if (! is_array($item)) {
                        continue;
                    }

                    $title = $this->cleanText($item['title'] ?? $item['pattern'] ?? $item['particle'] ?? 'Ngữ pháp');
                    $pattern = $this->cleanText($item['pattern'] ?? $item['structure'] ?? $title);
                    $note = $this->cleanText($item['meaning'] ?? $item['usage'] ?? $item['explain'] ?? $item['notes'][0] ?? '');

                    $rows[] = [
                        'title' => $title,
                        'pattern' => $pattern,
                        'note' => Str::limit($note, 220),
                    ];
                }

                return $rows;
            })
            ->values();
    }

    private function pickGrammar(array $context): array
    {
        $selected = trim((string) $context['request']['selected_text']);

        return collect($context['lesson']['grammar'])->first(function (array $item) use ($selected) {
            return $selected !== '' && (
                Str::contains($item['title'], $selected)
                || Str::contains($item['pattern'], $selected)
                || Str::contains($selected, $item['title'])
            );
        }) ?: (collect($context['lesson']['grammar'])->first() ?: []);
    }

    private function examplesFromContext(array $context, int $limit): array
    {
        $vocab = collect($context['lesson']['vocabulary'])->take($limit)->values();

        return $vocab->map(function (array $item) {
            return [
                'jp' => $item['japanese'],
                'vi' => $item['meaning'],
            ];
        })->all();
    }

    private function baseResponse(string $action, string $title, string $answer, array $context, array $bullets = [], array $examples = [], array $quiz = []): array
    {
        return [
            'action' => $action,
            'title' => $title,
            'answer' => $answer,
            'bullets' => array_values($bullets),
            'examples' => array_values($examples),
            'quiz' => array_values($quiz),
            'provider' => 'local',
            'model' => 'controlled-local',
            'context_summary' => $this->contextSummary($context),
        ];
    }

    private function contextSummary(array $context): array
    {
        return [
            'lesson' => 'Bài '.$context['lesson']['number'].' - '.$context['lesson']['title'],
            'level' => $context['user']['level'],
            'goal' => $context['user']['goal'],
            'daily_minutes' => $context['user']['daily_minutes'],
        ];
    }

    private function learningReasonLabel(array $context): string
    {
        $reason = $context['user']['learning_reasons'][0] ?? null;

        return $reason ? str_replace('_', ' ', (string) $reason) : 'mục tiêu học của bạn';
    }

    private function systemPrompt(): string
    {
        return 'Bạn là AI Tutor tiếng Nhật trong app học Minna no Nihongo. Chỉ trả lời bằng tiếng Việt, dùng tiếng Nhật khi cần. Chỉ dùng ngữ cảnh bài học, trình độ, mục tiêu và lỗi sai được cung cấp. Không bịa nguồn ngoài, không trả lời chủ đề ngoài học tiếng Nhật. Trả lời ngắn, có cấu trúc, phù hợp trình độ user.';
    }

    private function extractOpenAiText(array $payload): string
    {
        $parts = [];
        foreach (($payload['output'] ?? []) as $output) {
            foreach (($output['content'] ?? []) as $content) {
                $text = $content['text'] ?? null;
                if (is_string($text) && $text !== '') {
                    $parts[] = $text;
                }
            }
        }

        return implode("\n", $parts);
    }

    private function cleanText(mixed $value): string
    {
        if (is_array($value)) {
            $value = implode(' ', array_map(fn ($item) => is_scalar($item) ? (string) $item : '', $value));
        }

        return trim((string) $value);
    }
}
