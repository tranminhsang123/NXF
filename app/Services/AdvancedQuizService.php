<?php

namespace App\Services;

use App\Models\MinnaLesson;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class AdvancedQuizService
{
    private const VOCAB_KEYS = ['vocab', 'mau_cau', 'countries', 'proper_nouns', 'cau', 'places', 'rail'];

    public function build(MinnaLesson $lesson, int $limit = 8): array
    {
        $items = $this->extractStudyItems($lesson->sections);

        if ($items->count() < 3) {
            return [];
        }

        $questions = [];

        foreach ($items->take($limit * 2)->values() as $index => $item) {
            if (count($questions) >= $limit) {
                break;
            }

            $type = match ($index % 4) {
                0 => 'fill_blank',
                1 => 'translation',
                2 => 'rewrite',
                default => 'sentence_order',
            };

            if ($type === 'sentence_order') {
                $question = $this->buildSentenceOrderQuestion($item, count($questions));
                if (! $question) {
                    $type = 'translation';
                } else {
                    $questions[] = $question;
                    continue;
                }
            }

            $questions[] = $this->buildTextQuestion($item, $type, count($questions));
        }

        return array_values(array_filter($questions));
    }

    public function grade(array $questions, array $answers): array
    {
        $score = 0;
        $snapshot = [];

        foreach ($questions as $index => $question) {
            $answer = trim((string) ($answers[$index] ?? ''));
            $correct = $this->isCorrect($question, $answer);

            if ($correct) {
                $score++;
            }

            $snapshot[] = [
                'type' => $question['type'],
                'prompt' => $question['prompt'],
                'answer' => $question['answer'],
                'selected' => $answer,
                'correct' => $correct,
            ];
        }

        $total = count($questions);
        $percent = $total > 0 ? (int) round(($score / $total) * 100) : 0;

        return [
            'score' => $score,
            'total' => $total,
            'percent' => $percent,
            'passed' => $total > 0 && $percent >= 75,
            'answers_snapshot' => $snapshot,
        ];
    }

    private function extractStudyItems(Collection $sections): Collection
    {
        return $sections
            ->where('key', 'tu-vung')
            ->flatMap(function ($section) {
                $content = is_array($section->content) ? $section->content : [];
                $rows = [];

                foreach (self::VOCAB_KEYS as $group) {
                    foreach (($content[$group] ?? []) as $item) {
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
                            'group' => $group,
                        ];
                    }
                }

                return $rows;
            })
            ->unique(fn (array $item) => $item['japanese'].'|'.$item['meaning'])
            ->values();
    }

    private function buildTextQuestion(array $item, string $type, int $index): array
    {
        return [
            'id' => 'advanced_'.$index,
            'type' => $type,
            'prompt' => match ($type) {
                'fill_blank' => 'Điền nghĩa tiếng Việt cho từ/câu sau:',
                'rewrite' => 'Viết lại tiếng Nhật từ nghĩa sau:',
                default => 'Dịch nhanh sang tiếng Việt:',
            },
            'display' => match ($type) {
                'rewrite' => $item['meaning'],
                default => $item['japanese'],
            },
            'answer' => match ($type) {
                'rewrite' => $item['japanese'],
                default => $item['meaning'],
            },
            'hint' => $type === 'rewrite' ? 'Nhập tiếng Nhật.' : 'Có thể nhập nghĩa chính, không cần giống từng chữ.',
            'tokens' => [],
        ];
    }

    private function buildSentenceOrderQuestion(array $item, int $index): ?array
    {
        $text = $item['japanese'];
        $tokens = preg_split('/\s+/u', trim($text)) ?: [];
        $tokens = array_values(array_filter($tokens));

        if (count($tokens) < 3) {
            $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $tokens = collect($chars)
                ->chunk(2)
                ->map(fn ($chunk) => implode('', $chunk->all()))
                ->filter()
                ->values()
                ->all();
        }

        if (count($tokens) < 3 || count($tokens) > 10) {
            return null;
        }

        $shuffled = $tokens;
        sort($shuffled);

        if ($shuffled === $tokens) {
            $shuffled = array_reverse($tokens);
        }

        return [
            'id' => 'advanced_'.$index,
            'type' => 'sentence_order',
            'prompt' => 'Sắp xếp các mảnh sau thành câu đúng:',
            'display' => $item['meaning'],
            'answer' => implode('', $tokens),
            'hint' => 'Bấm các mảnh theo đúng thứ tự.',
            'tokens' => $shuffled,
        ];
    }

    private function isCorrect(array $question, string $answer): bool
    {
        $expected = (string) ($question['answer'] ?? '');

        if (($question['type'] ?? '') === 'sentence_order') {
            return $this->normalizeJapanese($answer) === $this->normalizeJapanese($expected);
        }

        if (($question['type'] ?? '') === 'rewrite') {
            return $this->normalizeJapanese($answer) === $this->normalizeJapanese($expected);
        }

        $normalizedAnswer = $this->normalizeMeaning($answer);
        $normalizedExpected = $this->normalizeMeaning($expected);

        if ($normalizedAnswer === '' || $normalizedExpected === '') {
            return false;
        }

        return $normalizedAnswer === $normalizedExpected
            || Str::contains($normalizedExpected, $normalizedAnswer)
            || Str::contains($normalizedAnswer, $normalizedExpected);
    }

    private function normalizeMeaning(string $value): string
    {
        $value = mb_strtolower(trim($value));
        $value = preg_replace('/[[:punct:]\s]+/u', ' ', $value) ?: '';

        return trim($value);
    }

    private function normalizeJapanese(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/\s+/u', '', $value) ?: '';
        $value = preg_replace('/[。、！？,.!?]+/u', '', $value) ?: '';

        return $value;
    }
}
