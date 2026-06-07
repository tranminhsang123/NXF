<?php

namespace App\Services;

use App\Models\FavoriteItem;
use App\Models\Kanji;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class GlobalSearchService
{
    private const VOCAB_GROUPS = ['vocab', 'countries', 'proper_nouns', 'places', 'rail'];

    private const PATTERN_GROUPS = ['mau_cau', 'cau'];

    private const CACHE_KEY = 'global_search:minna_index:v1';

    private const CACHE_TTL = 600;

    public function search(string $query, ?User $user = null, int $limit = 8): array
    {
        $query = $this->normalizeQuery($query);
        $limit = max(1, min($limit, 30));

        if ($query === '') {
            return $this->emptyResult('');
        }

        $needle = mb_strtolower($query);
        $index = $this->minnaIndex();

        $vocabulary = $this->filterRows($index['vocabulary'], $needle, $query, $limit);
        $sentencePatterns = $this->filterRows($index['sentence_patterns'], $needle, $query, $limit);
        $grammar = $this->filterRows($index['grammar'], $needle, $query, $limit);
        $lessons = $this->searchLessons($query, $limit);
        $kanji = $this->searchKanji($query, $limit);
        $favorites = $user ? $this->searchFavorites($user, $needle, $limit) : [];
        $related = $this->buildRelated($query, $needle, [
            'vocabulary' => $vocabulary,
            'sentence_patterns' => $sentencePatterns,
            'grammar' => $grammar,
            'lessons' => $lessons,
            'kanji' => $kanji,
            'favorites' => $favorites,
        ], $index, $limit);

        return [
            'query' => $query,
            'vocabulary' => $vocabulary,
            'kanji' => $kanji,
            'lessons' => $lessons,
            'sentence_patterns' => $sentencePatterns,
            'grammar' => $grammar,
            'favorites' => $favorites,
            'related' => $related,
            'counts' => [
                'vocabulary' => count($vocabulary),
                'kanji' => count($kanji),
                'lessons' => count($lessons),
                'sentence_patterns' => count($sentencePatterns),
                'grammar' => count($grammar),
                'favorites' => count($favorites),
                'related' => count($related),
            ],
        ];
    }

    private function emptyResult(string $query): array
    {
        return [
            'query' => $query,
            'vocabulary' => [],
            'kanji' => [],
            'lessons' => [],
            'sentence_patterns' => [],
            'grammar' => [],
            'favorites' => [],
            'related' => [],
            'counts' => [
                'vocabulary' => 0,
                'kanji' => 0,
                'lessons' => 0,
                'sentence_patterns' => 0,
                'grammar' => 0,
                'favorites' => 0,
                'related' => 0,
            ],
        ];
    }

    private function minnaIndex(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $sections = MinnaSection::query()
                ->published()
                ->whereHas('lesson', fn ($q) => $q->published())
                ->with('lesson:id,number,title')
                ->get(['id', 'lesson_id', 'key', 'content']);

            $vocabulary = [];
            $sentencePatterns = [];
            $grammar = [];

            foreach ($sections as $section) {
                $content = is_array($section->content) ? $section->content : [];
                $base = [
                    'lesson_number' => $section->lesson?->number,
                    'lesson_title' => $section->lesson?->title,
                ];

                if ($section->key === 'tu-vung') {
                    foreach (self::VOCAB_GROUPS as $group) {
                        foreach (($content[$group] ?? []) as $item) {
                            if (! is_array($item)) {
                                continue;
                            }
                            $row = $this->mapVocabRow($item, $group, $base);
                            if ($row) {
                                $vocabulary[] = $row;
                            }
                        }
                    }
                    foreach (self::PATTERN_GROUPS as $group) {
                        foreach (($content[$group] ?? []) as $item) {
                            if (! is_array($item)) {
                                continue;
                            }
                            $row = $this->mapPatternRow($item, $group, $base);
                            if ($row) {
                                $sentencePatterns[] = $row;
                            }
                        }
                    }
                } elseif ($section->key === 'ngu-phap') {
                    foreach ($content as $item) {
                        if (! is_array($item)) {
                            continue;
                        }
                        $row = $this->mapGrammarRow($item, $base);
                        if ($row) {
                            $grammar[] = $row;
                        }
                    }
                }
            }

            return [
                'vocabulary' => $vocabulary,
                'sentence_patterns' => $sentencePatterns,
                'grammar' => $grammar,
            ];
        });
    }

    private function mapVocabRow(array $item, string $group, array $base): ?array
    {
        $term = trim((string) ($item['tu_vung'] ?? $item['jp'] ?? ''));
        $meaning = trim((string) ($item['nghia'] ?? ''));
        if ($term === '' && $meaning === '') {
            return null;
        }

        return array_merge($base, [
            'term' => $term,
            'reading' => trim((string) ($item['am_han'] ?? $item['romaji'] ?? '')),
            'kanji' => trim((string) ($item['han_tu'] ?? '')),
            'meaning' => $meaning,
            'group' => $group,
            'search_text' => implode(' ', array_filter([$term, $meaning, $item['han_tu'] ?? '', $item['am_han'] ?? ''])),
        ]);
    }

    private function mapPatternRow(array $item, string $group, array $base): ?array
    {
        $jp = trim((string) ($item['jp'] ?? $item['tu_vung'] ?? ''));
        $meaning = trim((string) ($item['nghia'] ?? ''));
        if ($jp === '' && $meaning === '') {
            return null;
        }

        return array_merge($base, [
            'pattern' => $jp,
            'meaning' => $meaning,
            'group' => $group,
            'search_text' => $jp.' '.$meaning,
        ]);
    }

    private function mapGrammarRow(array $item, array $base): ?array
    {
        $title = trim((string) ($item['title'] ?? ''));
        $pattern = $item['pattern'] ?? '';
        $patternText = is_array($pattern)
            ? implode(' ', array_map('strval', $pattern))
            : trim((string) $pattern);
        $explain = $item['explain'] ?? [];
        $explainText = is_array($explain)
            ? implode(' ', array_map('strval', $explain))
            : trim((string) $explain);

        if ($title === '' && $patternText === '' && $explainText === '') {
            return null;
        }

        return array_merge($base, [
            'title' => $title ?: 'Ngữ pháp',
            'pattern' => $patternText,
            'explain' => $explainText,
            'search_text' => implode(' ', array_filter([$title, $patternText, $explainText])),
        ]);
    }

    private function filterRows(array $rows, string $needle, string $term, int $limit): array
    {
        return collect($rows)
            ->filter(fn (array $row) => $this->rowMatches($row, $needle, $term))
            ->sortBy(fn (array $row) => $this->matchRank($row, $needle, $term, ['term', 'kanji', 'pattern', 'title', 'search_text']))
            ->take($limit)
            ->values()
            ->map(function (array $row) {
                unset($row['search_text']);

                return $row;
            })
            ->all();
    }

    private function rowMatches(array $row, string $needle, string $term): bool
    {
        $haystack = mb_strtolower((string) ($row['search_text'] ?? ''));
        if ($haystack !== '' && Str::contains($haystack, $needle)) {
            return true;
        }

        foreach (['term', 'kanji', 'pattern', 'title', 'meaning'] as $field) {
            $value = mb_strtolower((string) ($row[$field] ?? ''));
            if ($value !== '' && Str::contains($value, $needle)) {
                return true;
            }
        }

        return false;
    }

    private function matchRank(array $row, string $needle, string $term, array $primaryFields): int
    {
        foreach ($primaryFields as $field) {
            $value = (string) ($row[$field] ?? '');
            if ($value === $term) {
                return 0;
            }
            if (mb_strtolower($value) === $needle) {
                return 1;
            }
        }

        return 2;
    }

    private function searchLessons(string $query, int $limit): array
    {
        $q = MinnaLesson::query()->published();

        if (ctype_digit($query)) {
            $q->where(function ($builder) use ($query) {
                $builder->where('number', (int) $query)
                    ->orWhere('title', 'like', '%'.$query.'%')
                    ->orWhere('description', 'like', '%'.$query.'%');
            });
        } else {
            $q->where(function ($builder) use ($query) {
                $builder->where('title', 'like', '%'.$query.'%')
                    ->orWhere('description', 'like', '%'.$query.'%');
            });
        }

        return $q->orderBy('number')
            ->limit($limit)
            ->get(['id', 'number', 'title', 'description'])
            ->map(fn (MinnaLesson $lesson) => [
                'id' => $lesson->id,
                'number' => $lesson->number,
                'title' => $lesson->title,
                'description' => $lesson->description,
                'url' => route('minna.show', $lesson->number),
            ])
            ->all();
    }

    private function searchKanji(string $query, int $limit): array
    {
        return Kanji::query()
            ->published()
            ->where(function ($q) use ($query) {
                $q->where('character', 'like', '%'.$query.'%')
                    ->orWhere('meaning', 'like', '%'.$query.'%')
                    ->orWhere('on_reading', 'like', '%'.$query.'%')
                    ->orWhere('kun_reading', 'like', '%'.$query.'%');
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
                'url' => route('kanji.list', $kanji->level),
            ])
            ->all();
    }

    private function searchFavorites(User $user, string $needle, int $limit): array
    {
        return FavoriteItem::query()
            ->where('user_id', $user->id)
            ->where(function ($q) use ($needle) {
                $q->whereRaw('LOWER(front) LIKE ?', ['%'.$needle.'%'])
                    ->orWhereRaw('LOWER(back) LIKE ?', ['%'.$needle.'%']);
            })
            ->latest('id')
            ->limit($limit)
            ->get(['id', 'front', 'back', 'item_type', 'lesson_number'])
            ->map(fn (FavoriteItem $item) => [
                'id' => $item->id,
                'front' => $item->front,
                'back' => $item->back,
                'item_type' => $item->item_type,
                'lesson_number' => $item->lesson_number,
                'url' => route('flashcard.index'),
            ])
            ->all();
    }

    private function buildRelated(string $query, string $needle, array $results, array $index, int $limit): array
    {
        $related = [];
        $seen = [];

        $lessonNumbers = collect($results)
            ->only(['vocabulary', 'sentence_patterns', 'grammar'])
            ->flatten(1)
            ->pluck('lesson_number')
            ->filter()
            ->unique()
            ->take(3);

        foreach ($lessonNumbers as $number) {
            $extras = collect($index['vocabulary'])
                ->where('lesson_number', $number)
                ->reject(fn (array $row) => $this->rowMatches($row, $needle, $query))
                ->take(2);

            foreach ($extras as $row) {
                $key = 'v:'.$row['term'].'|'.$row['meaning'];
                if (isset($seen[$key])) {
                    continue;
                }
                $seen[$key] = true;
                unset($row['search_text']);
                $related[] = [
                    'type' => 'vocabulary',
                    'reason' => 'Cùng bài '.$number,
                    'item' => $row,
                    'url' => route('minna.show', $number),
                ];
            }
        }

        $matchedKanji = collect($results['kanji'] ?? [])->pluck('character')->all();
        $jpText = collect($results['vocabulary'] ?? [])
            ->pluck('term')
            ->merge(collect($results['sentence_patterns'] ?? [])->pluck('pattern'))
            ->implode('');

        preg_match_all('/[\x{4E00}-\x{9FFF}]/u', $jpText, $chars);
        $chars = array_unique(array_diff($chars[0] ?? [], $matchedKanji));

        foreach (array_slice($chars, 0, 3) as $char) {
            $kanji = Kanji::query()->published()->where('character', $char)->first();
            if (! $kanji) {
                continue;
            }
            $key = 'k:'.$kanji->character;
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $related[] = [
                'type' => 'kanji',
                'reason' => 'Hán tự trong kết quả',
                'item' => [
                    'character' => $kanji->character,
                    'meaning' => $kanji->meaning,
                    'level' => $kanji->level,
                ],
                'url' => route('kanji.list', $kanji->level),
            ];
        }

        if (count($related) < $limit && ! empty($results['grammar'])) {
            $grammarLesson = $results['grammar'][0]['lesson_number'] ?? null;
            if ($grammarLesson) {
                $patterns = collect($index['sentence_patterns'])
                    ->where('lesson_number', $grammarLesson)
                    ->reject(fn (array $row) => $this->rowMatches($row, $needle, $query))
                    ->take(2);
                foreach ($patterns as $row) {
                    unset($row['search_text']);
                    $related[] = [
                        'type' => 'sentence_pattern',
                        'reason' => 'Mẫu câu cùng bài',
                        'item' => $row,
                        'url' => route('minna.show', $grammarLesson),
                    ];
                    if (count($related) >= $limit) {
                        break;
                    }
                }
            }
        }

        return array_slice($related, 0, $limit);
    }

    private function normalizeQuery(string $query): string
    {
        $query = trim(strip_tags($query));
        $query = preg_replace('/[。、！？,.!?;:()\[\]{}"“”]+/u', '', $query) ?: '';
        $query = preg_replace('/\s+/u', ' ', $query) ?: '';

        return mb_substr(trim($query), 0, 80);
    }
}
