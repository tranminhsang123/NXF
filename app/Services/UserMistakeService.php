<?php

namespace App\Services;

use App\Models\FlashcardCardState;
use App\Models\MinnaQuizAttempt;
use App\Models\MinnaSection;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class UserMistakeService
{
    private const VOCAB_KEYS = ['vocab', 'mau_cau', 'countries', 'proper_nouns', 'cau', 'places', 'rail'];

    public function summary(User $user): array
    {
        $attempts = $this->recentAttempts($user, 20);
        $wrongAnswers = $this->wrongAnswers($attempts);
        $lowScoreLessons = $this->lowScoreLessons($attempts, 3);
        $weakFlashcards = $this->weakFlashcards($user, 3);

        return [
            'wrong_vocab_count' => $wrongAnswers->where('category', 'vocabulary')->count(),
            'wrong_grammar_count' => $wrongAnswers->where('category', 'grammar')->count(),
            'wrong_quiz_count' => $wrongAnswers->count(),
            'low_lesson_count' => $lowScoreLessons->count(),
            'weak_flashcard_count' => $weakFlashcards->count(),
            'review_url' => $this->reviewUrl($weakFlashcards, $lowScoreLessons),
            'page_url' => route('user.mistakes'),
        ];
    }

    public function build(User $user): array
    {
        $attempts = $this->recentAttempts($user, 40);
        $wrongAnswers = $this->wrongAnswers($attempts);
        $lowScoreLessons = $this->lowScoreLessons($attempts, 6);
        $weakFlashcards = $this->weakFlashcards($user, 10);
        $weakVocabulary = $this->weakVocabulary($wrongAnswers, $weakFlashcards, 10);
        $weakGrammar = $this->weakGrammar($wrongAnswers, $lowScoreLessons, 8);

        return [
            'summary' => [
                'wrong_vocab_count' => $weakVocabulary->count(),
                'wrong_grammar_count' => $weakGrammar->count(),
                'wrong_quiz_count' => $wrongAnswers->count(),
                'low_lesson_count' => $lowScoreLessons->count(),
                'weak_flashcard_count' => $weakFlashcards->count(),
            ],
            'weak_vocabulary' => $weakVocabulary,
            'weak_grammar' => $weakGrammar,
            'wrong_quiz_answers' => $wrongAnswers->take(12)->values(),
            'low_score_lessons' => $lowScoreLessons,
            'weak_flashcards' => $weakFlashcards,
            'review_plan' => $this->reviewPlan($wrongAnswers, $lowScoreLessons, $weakFlashcards, $weakGrammar),
        ];
    }

    private function recentAttempts(User $user, int $limit): Collection
    {
        return MinnaQuizAttempt::query()
            ->where('user_id', $user->id)
            ->with('lesson:id,number,title')
            ->latest('completed_at')
            ->limit($limit)
            ->get();
    }

    private function wrongAnswers(Collection $attempts): Collection
    {
        return $attempts
            ->flatMap(function (MinnaQuizAttempt $attempt) {
                $snapshot = $attempt->answers_snapshot ?? [];
                $mode = is_array($snapshot) && ($snapshot['mode'] ?? null) === 'advanced' ? 'advanced' : 'basic';
                $rows = $mode === 'advanced' ? ($snapshot['answers'] ?? []) : $snapshot;

                if (! is_array($rows)) {
                    return [];
                }

                return collect($rows)
                    ->filter(fn ($row) => is_array($row) && ! (bool) ($row['correct'] ?? false))
                    ->map(function (array $row) use ($attempt, $mode) {
                        $rawType = (string) ($row['type'] ?? 'multiple_choice');
                        $category = in_array($rawType, ['sentence_order', 'rewrite'], true) ? 'grammar' : 'vocabulary';
                        $lesson = $attempt->lesson;

                        return [
                            'attempt_id' => $attempt->id,
                            'lesson_id' => $attempt->minna_lesson_id,
                            'lesson_number' => $lesson?->number,
                            'lesson_title' => $lesson?->title,
                            'mode' => $mode,
                            'type' => $this->questionTypeLabel($rawType),
                            'raw_type' => $rawType,
                            'category' => $category,
                            'prompt' => $this->cleanText($row['display'] ?? $row['prompt'] ?? $row['answer'] ?? 'Câu hỏi'),
                            'answer' => $this->cleanText($row['answer'] ?? ''),
                            'selected' => $this->cleanText($row['selected'] ?? '') ?: 'Chưa trả lời',
                            'completed_at' => $attempt->completed_at,
                            'lesson_url' => $lesson ? route('minna.show', ['number' => $lesson->number]) : route('minna.index'),
                            'quiz_url' => $lesson ? route('minna.quiz.advanced', ['number' => $lesson->number]) : route('minna.index'),
                        ];
                    });
            })
            ->sortByDesc(fn (array $item) => $item['completed_at']?->timestamp ?? 0)
            ->values();
    }

    private function weakVocabulary(Collection $wrongAnswers, Collection $weakFlashcards, int $limit): Collection
    {
        $fromQuiz = $wrongAnswers
            ->where('category', 'vocabulary')
            ->groupBy(fn (array $item) => mb_strtolower(($item['prompt'] ?? '').'|'.($item['answer'] ?? '')))
            ->map(function (Collection $items) {
                $first = $items->first();

                return [
                    'source' => 'quiz',
                    'front' => $first['prompt'],
                    'back' => $first['answer'],
                    'mistake_count' => $items->count(),
                    'latest_selected' => $first['selected'],
                    'lesson_numbers' => $items->pluck('lesson_number')->filter()->unique()->values()->all(),
                    'url' => $first['lesson_url'],
                ];
            })
            ->values();

        $fromFlashcards = $weakFlashcards->map(fn (array $card) => [
            'source' => 'flashcard',
            'front' => $card['front'],
            'back' => $card['back'],
            'mistake_count' => max(1, (int) ($card['lapses'] ?? 0)),
            'latest_selected' => 'Độ nhớ gần nhất: '.($card['last_quality'] ?? '-').'/5',
            'lesson_numbers' => array_values(array_filter([$card['lesson_number'] ?? null])),
            'url' => $card['review_url'],
        ]);

        return $fromQuiz
            ->merge($fromFlashcards)
            ->sortByDesc('mistake_count')
            ->take($limit)
            ->values();
    }

    private function weakGrammar(Collection $wrongAnswers, Collection $lowScoreLessons, int $limit): Collection
    {
        $fromWrongAnswers = $wrongAnswers
            ->where('category', 'grammar')
            ->map(fn (array $item) => [
                'source' => 'quiz',
                'title' => $item['answer'] ?: $item['prompt'],
                'note' => 'Sai trong dạng '.$item['type'].'. Bạn đã trả lời: '.$item['selected'],
                'lesson_number' => $item['lesson_number'],
                'lesson_title' => $item['lesson_title'],
                'url' => $item['lesson_url'],
            ]);

        $lessonIds = $lowScoreLessons->pluck('lesson_id')->filter()->values()->all();
        $grammarSections = empty($lessonIds)
            ? collect()
            : MinnaSection::query()
                ->whereIn('lesson_id', $lessonIds)
                ->where('key', 'ngu-phap')
                ->with('lesson:id,number,title')
                ->get();

        $fromLowLessons = $grammarSections->flatMap(function (MinnaSection $section) {
            return collect($this->extractGrammarPoints($section->content ?? []))
                ->take(2)
                ->map(fn (array $point) => [
                    'source' => 'low_score_lesson',
                    'title' => $point['title'],
                    'note' => $point['note'],
                    'lesson_number' => $section->lesson?->number,
                    'lesson_title' => $section->lesson?->title,
                    'url' => $section->lesson ? route('minna.section', [
                        'number' => $section->lesson->number,
                        'sectionKey' => 'ngu-phap',
                    ]) : route('minna.index'),
                ]);
        });

        return $fromWrongAnswers
            ->merge($fromLowLessons)
            ->unique(fn (array $item) => ($item['lesson_number'] ?? '').'|'.$item['title'])
            ->take($limit)
            ->values();
    }

    private function lowScoreLessons(Collection $attempts, int $limit): Collection
    {
        return $attempts
            ->groupBy('minna_lesson_id')
            ->map(function (Collection $items) {
                /** @var MinnaQuizAttempt $latest */
                $latest = $items->sortByDesc(fn (MinnaQuizAttempt $attempt) => $attempt->completed_at?->timestamp ?? 0)->first();
                $lesson = $latest?->lesson;
                $bestPercent = (int) $items->max('percent');
                $avgPercent = (int) round($items->avg('percent') ?? 0);
                $failedCount = $items->where('passed', false)->count();

                return [
                    'lesson_id' => $latest?->minna_lesson_id,
                    'lesson_number' => $lesson?->number,
                    'lesson_title' => $lesson?->title,
                    'attempt_count' => $items->count(),
                    'failed_count' => $failedCount,
                    'latest_percent' => (int) ($latest?->percent ?? 0),
                    'best_percent' => $bestPercent,
                    'avg_percent' => $avgPercent,
                    'completed_at' => $latest?->completed_at,
                    'url' => $lesson ? route('minna.show', ['number' => $lesson->number]) : route('minna.index'),
                    'quiz_url' => $lesson ? route('minna.quiz.advanced', ['number' => $lesson->number]) : route('minna.index'),
                ];
            })
            ->filter(fn (array $item) => $item['failed_count'] > 0 || $item['latest_percent'] < 80 || $item['best_percent'] < 80)
            ->sortBy([
                ['best_percent', 'asc'],
                ['latest_percent', 'asc'],
            ])
            ->take($limit)
            ->values();
    }

    private function weakFlashcards(User $user, int $limit): Collection
    {
        return FlashcardCardState::query()
            ->where('user_id', $user->id)
            ->where(function ($query) {
                $query
                    ->where('lapses', '>', 0)
                    ->orWhere('last_quality', '<', 3)
                    ->orWhere('ease_factor', '<=', 1.8);
            })
            ->with('minnaSection.lesson:id,number,title')
            ->orderByDesc('lapses')
            ->orderBy('last_quality')
            ->orderBy('ease_factor')
            ->limit($limit * 3)
            ->get()
            ->map(function (FlashcardCardState $state) {
                $section = $state->minnaSection;
                $card = $section ? $this->resolveCard($section, (int) $state->card_index) : null;

                if (! $section || ! $card) {
                    return null;
                }

                $lesson = $section->lesson;

                return [
                    'front' => $card['front'],
                    'back' => $card['back'],
                    'lesson_number' => $lesson?->number,
                    'lesson_title' => $lesson?->title,
                    'lapses' => (int) $state->lapses,
                    'last_quality' => $state->last_quality,
                    'ease_factor' => $state->ease_factor,
                    'next_review_at' => $state->next_review_at,
                    'review_url' => $lesson ? route('flashcard.study.multi', [
                        'bai' => (string) $lesson->number,
                        'mode' => 'srs',
                    ]) : route('flashcard.index'),
                ];
            })
            ->filter()
            ->take($limit)
            ->values();
    }

    private function reviewPlan(Collection $wrongAnswers, Collection $lowScoreLessons, Collection $weakFlashcards, Collection $weakGrammar): array
    {
        $steps = [];

        if ($weakFlashcards->isNotEmpty()) {
            $steps[] = [
                'title' => 'Ôn flashcard hay quên',
                'detail' => 'Làm 3-5 thẻ có lapses hoặc điểm nhớ thấp trước.',
                'url' => $this->reviewUrl($weakFlashcards, $lowScoreLessons),
            ];
        }

        if ($lowScoreLessons->isNotEmpty()) {
            $lesson = $lowScoreLessons->first();
            $steps[] = [
                'title' => 'Làm lại quiz bài '.$lesson['lesson_number'],
                'detail' => 'Bài này đang có điểm gần nhất '.$lesson['latest_percent'].'%.',
                'url' => $lesson['quiz_url'],
            ];
        }

        if ($weakGrammar->isNotEmpty()) {
            $grammar = $weakGrammar->first();
            $steps[] = [
                'title' => 'Xem lại ngữ pháp',
                'detail' => $grammar['title'],
                'url' => $grammar['url'],
            ];
        }

        if ($wrongAnswers->isNotEmpty()) {
            $steps[] = [
                'title' => 'Đọc lại câu quiz từng làm sai',
                'detail' => 'So sánh đáp án đúng với câu trả lời gần nhất của bạn.',
                'url' => route('user.mistakes').'#wrong-quiz',
            ];
        }

        if ($steps === []) {
            $steps[] = [
                'title' => 'Chưa có lỗi sai rõ ràng',
                'detail' => 'Hãy làm một quiz hoặc ôn flashcard SRS để hệ thống bắt đầu cá nhân hóa.',
                'url' => route('minna.index'),
            ];
        }

        return [
            'minutes' => 5,
            'primary_label' => 'Ôn lại ngay',
            'primary_url' => $steps[0]['url'],
            'steps' => $steps,
        ];
    }

    private function reviewUrl(Collection $weakFlashcards, Collection $lowScoreLessons): string
    {
        $lessonNumbers = $weakFlashcards
            ->pluck('lesson_number')
            ->merge($lowScoreLessons->pluck('lesson_number'))
            ->filter()
            ->unique()
            ->take(5)
            ->values()
            ->all();

        if ($lessonNumbers === []) {
            return route('flashcard.index');
        }

        return route('flashcard.study.multi', [
            'bai' => implode(',', $lessonNumbers),
            'mode' => 'srs',
        ]);
    }

    private function extractGrammarPoints(array $content): array
    {
        $points = [];

        foreach ($content as $item) {
            if (! is_array($item)) {
                continue;
            }

            $title = $this->cleanText($item['title'] ?? $item['pattern'] ?? $item['particle'] ?? 'Điểm ngữ pháp');
            $note = $this->cleanText($item['meaning'] ?? $item['usage'] ?? $item['explain'] ?? $item['notes'][0] ?? 'Xem lại giải thích và ví dụ của điểm ngữ pháp này.');

            $points[] = [
                'title' => $title,
                'note' => Str::limit($note, 140),
            ];
        }

        return $points;
    }

    private function resolveCard(MinnaSection $section, int $cardIndex): ?array
    {
        $cards = $this->extractCards($section->content ?? []);

        return $cards[$cardIndex] ?? null;
    }

    private function extractCards(array $content): array
    {
        $cards = [];

        foreach (self::VOCAB_KEYS as $key) {
            foreach (($content[$key] ?? []) as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $front = $this->cleanText($item['tu_vung'] ?? $item['jp'] ?? '');
                $meaning = $this->cleanText($item['nghia'] ?? '');

                if ($front === '' || $meaning === '') {
                    continue;
                }

                $parts = [$meaning];
                if (! empty($item['han_tu']) && $item['han_tu'] !== '-') {
                    $parts[] = 'Hán tự: '.$item['han_tu'];
                }
                if (! empty($item['loai_tu'])) {
                    $parts[] = (string) $item['loai_tu'];
                }

                $cards[] = [
                    'front' => $front,
                    'back' => implode(' - ', array_filter($parts)),
                ];
            }
        }

        return $cards;
    }

    private function questionTypeLabel(string $type): string
    {
        return match ($type) {
            'fill_blank' => 'Điền vào chỗ trống',
            'translation' => 'Dịch nghĩa',
            'rewrite' => 'Viết lại',
            'sentence_order' => 'Sắp xếp câu',
            default => 'Trắc nghiệm',
        };
    }

    private function cleanText(mixed $value): string
    {
        if (is_array($value)) {
            $value = implode(' ', array_map(fn ($item) => is_scalar($item) ? (string) $item : '', $value));
        }

        return trim((string) $value);
    }
}
