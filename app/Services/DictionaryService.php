<?php

namespace App\Services;

use App\Models\Kanji;
use App\Models\MinnaSection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DictionaryService
{
    private const VOCAB_KEYS = ['vocab', 'mau_cau', 'countries', 'proper_nouns', 'cau', 'places', 'rail'];

    public function lookup(string $term, int $limit = 8): array
    {
        $term = $this->normalizeTerm($term);
        $limit = max(1, min($limit, 20));

        if ($term === '') {
            return [
                'query' => '',
                'entries' => [],
                'kanji' => [],
            ];
        }

        return [
            'query' => $term,
            'entries' => $this->lookupVocabulary($term, $limit),
            'kanji' => $this->lookupKanji($term, $limit),
        ];
    }

    private function lookupVocabulary(string $term, int $limit): array
    {
        $items = Cache::remember('dictionary:minna_vocab:v1', 600, function () {
            return MinnaSection::query()
                ->where('key', 'tu-vung')
                ->with('lesson:id,number,title')
                ->get(['id', 'lesson_id', 'content'])
                ->flatMap(function (MinnaSection $section) {
                    $content = is_array($section->content) ? $section->content : [];
                    $rows = [];

                    foreach (self::VOCAB_KEYS as $group) {
                        foreach (($content[$group] ?? []) as $item) {
                            if (! is_array($item)) {
                                continue;
                            }

                            $front = trim((string) ($item['tu_vung'] ?? $item['jp'] ?? ''));
                            $meaning = trim((string) ($item['nghia'] ?? ''));

                            if ($front === '' && $meaning === '') {
                                continue;
                            }

                            $rows[] = [
                                'term' => $front,
                                'reading' => trim((string) ($item['am_han'] ?? '')),
                                'kanji' => trim((string) ($item['han_tu'] ?? '')),
                                'meaning' => $meaning,
                                'note' => trim((string) ($item['ghi_chu'] ?? '')),
                                'part_of_speech' => $item['loai_tu'] ?? null,
                                'group' => $group,
                                'lesson_number' => $section->lesson?->number,
                                'lesson_title' => $section->lesson?->title,
                            ];
                        }
                    }

                    return $rows;
                })
                ->values()
                ->all();
        });

        $needle = mb_strtolower($term);
        $matches = collect($items)
            ->filter(function (array $item) use ($needle) {
                foreach (['term', 'kanji', 'reading', 'meaning'] as $field) {
                    $value = mb_strtolower((string) ($item[$field] ?? ''));
                    if ($value !== '' && Str::contains($value, $needle)) {
                        return true;
                    }
                }

                return false;
            })
            ->sortBy(function (array $item) use ($term, $needle) {
                $termValue = (string) ($item['term'] ?? '');
                $kanjiValue = (string) ($item['kanji'] ?? '');
                if ($termValue === $term || $kanjiValue === $term) {
                    return 0;
                }
                if (mb_strtolower($termValue) === $needle || mb_strtolower($kanjiValue) === $needle) {
                    return 1;
                }

                return 2;
            })
            ->take($limit)
            ->values()
            ->all();

        return $matches;
    }

    private function lookupKanji(string $term, int $limit): array
    {
        return Kanji::query()
            ->where(function ($query) use ($term) {
                $query->where('character', 'like', '%'.$term.'%')
                    ->orWhere('meaning', 'like', '%'.$term.'%')
                    ->orWhere('on_reading', 'like', '%'.$term.'%')
                    ->orWhere('kun_reading', 'like', '%'.$term.'%');
            })
            ->orderBy('level')
            ->limit($limit)
            ->get(['id', 'character', 'meaning', 'on_reading', 'kun_reading', 'level'])
            ->map(fn (Kanji $kanji) => [
                'id' => $kanji->id,
                'character' => $kanji->character,
                'meaning' => $kanji->meaning,
                'on_reading' => $kanji->on_reading,
                'kun_reading' => $kanji->kun_reading,
                'level' => $kanji->level,
            ])
            ->all();
    }

    private function normalizeTerm(string $term): string
    {
        $term = trim(strip_tags($term));
        $term = preg_replace('/[。、！？,.!?;:()\[\]{}"“”]+/u', '', $term) ?: '';
        $term = preg_replace('/\s+/u', ' ', $term) ?: '';

        return mb_substr(trim($term), 0, 80);
    }
}
