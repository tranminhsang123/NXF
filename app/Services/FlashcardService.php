<?php

namespace App\Services;

use App\Models\FlashcardCardState;
use App\Models\FavoriteItem;
use App\Models\MinnaSection;
use App\Models\User;
use App\Support\Cache\FlashcardCache;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class FlashcardService
{
    private const VOCAB_KEYS = ['vocab', 'mau_cau', 'countries', 'proper_nouns', 'cau', 'places', 'rail'];

    private const LOAI_TU = ['danh_tu' => 'Danh từ', 'dong_tu' => 'Động từ', 'tinh_tu' => 'Tính từ'];

    private const CACHE_TTL = 600;

    /** Danh sách bài có từ vựng + số thẻ (có cache) */
    public function getLessonsWithVocabCount(): Collection
    {
        return Cache::remember('flashcard:lessons', self::CACHE_TTL, function () {
            return MinnaSection::select('id', 'lesson_id', 'content')
                ->where('key', 'tu-vung')
                ->published()
                ->whereHas('lesson', fn ($q) => $q->published())
                ->whereNotNull('content')
                ->with('lesson:id,number,title')
                ->get()
                ->map(function (MinnaSection $s) {
                    $count = $this->countCards($s->content ?? []);

                    return $s->lesson ? ['lesson' => $s->lesson, 'count' => $count] : null;
                })
                ->filter()
                ->values();
        });
    }

    /** Flashcard theo 1 hoặc nhiều bài */
    public function getFlashcardsByLessons(array $numbers, bool $shuffle = false): array
    {
        $numbers = array_values(array_unique(array_filter(array_map('intval', $numbers))));
        if (empty($numbers)) {
            return ['lessons' => [], 'cards' => []];
        }

        $v = FlashcardCache::currentBaseVersion();
        $baseCacheKey = 'flashcards:base:v'.$v.':'.implode(',', $numbers);

        $base = Cache::remember($baseCacheKey, self::CACHE_TTL, function () use ($numbers) {
            $sections = MinnaSection::where('key', 'tu-vung')
                ->published()
                ->whereHas('lesson', fn ($q) => $q->published()->whereIn('number', $numbers))
                ->with('lesson:id,number,title')
                ->orderBy('lesson_id')
                ->get();

            $lessons = [];
            $allCards = [];
            foreach ($sections as $section) {
                if (! $section->lesson) {
                    continue;
                }
                $cards = $this->extractCards((int) $section->id, $section->content ?? []);
                foreach ($cards as $c) {
                    $c['lesson_number'] = $section->lesson->number;
                    $allCards[] = $c;
                }
                $lessons[$section->lesson->number] = $section->lesson;
            }

            return [
                'lessons' => array_values($lessons),
                'cards' => $allCards,
            ];
        });

        $cards = $base['cards'];
        if ($shuffle) {
            shuffle($cards);
        }

        return [
            'lessons' => $base['lessons'],
            'cards' => $cards,
        ];
    }

    /**
     * Bộ thẻ cho chế độ SRS (SM-2): ưu tiên thẻ đến hạn, sau đó thẻ mới (giới hạn mỗi phiên).
     *
     * @return array{lessons: array, cards: array, stats: array{due_count: int, new_count: int, total_in_scope: int}}
     */
    public function getFlashcardsForSrs(User $user, array $numbers, int $maxNewPerSession = 40): array
    {
        $base = $this->getFlashcardsByLessons($numbers, false);
        $cards = $base['cards'];
        $totalInScope = count($cards);

        if ($totalInScope === 0) {
            return [
                'lessons' => [],
                'cards' => [],
                'stats' => ['due_count' => 0, 'new_count' => 0, 'total_in_scope' => 0],
            ];
        }

        $sectionIds = collect($cards)->pluck('section_id')->unique()->filter()->values();
        $states = FlashcardCardState::query()
            ->where('user_id', $user->id)
            ->whereIn('minna_section_id', $sectionIds)
            ->get()
            ->keyBy(fn (FlashcardCardState $s) => $s->minna_section_id.'_'.$s->card_index);

        $now = now();
        $due = [];
        $new = [];

        foreach ($cards as $c) {
            $sid = $c['section_id'] ?? null;
            $idx = $c['card_index'] ?? null;
            if ($sid === null || $idx === null) {
                continue;
            }
            $key = $sid.'_'.$idx;
            $state = $states->get($key);
            if (! $state) {
                $new[] = $c;
            } elseif ($state->next_review_at && $state->next_review_at->lte($now)) {
                $due[] = $c;
            }
        }

        shuffle($new);
        $newLimited = array_slice($new, 0, $maxNewPerSession);
        $deck = array_merge($due, $newLimited);
        shuffle($deck);

        return [
            'lessons' => $base['lessons'],
            'cards' => $deck,
            'stats' => [
                'due_count' => count($due),
                'new_count' => count($newLimited),
                'total_in_scope' => $totalInScope,
            ],
        ];
    }

    /**
     * Lightweight summary for dashboards and learning plans.
     *
     * @return array{due_count: int, new_count: int, total_in_scope: int}
     */
    public function getSrsSummary(User $user, array $numbers, int $maxNewPerSession = 40): array
    {
        $result = $this->getFlashcardsForSrs($user, $numbers, $maxNewPerSession);

        return $result['stats'];
    }

    public function getSrsDashboard(User $user): array
    {
        $now = Carbon::now();
        $tomorrow = $now->copy()->addDay();

        $states = FlashcardCardState::query()
            ->where('user_id', $user->id)
            ->with('minnaSection.lesson:id,number,title')
            ->get();

        $due = $states->filter(fn (FlashcardCardState $state) => $state->next_review_at && $state->next_review_at->lte($now));
        $upcoming = $states->filter(
            fn (FlashcardCardState $state) => $state->next_review_at
                && $state->next_review_at->gt($now)
                && $state->next_review_at->lte($tomorrow)
        );
        $weak = $states->filter(
            fn (FlashcardCardState $state) => (int) $state->last_quality < 3 || (float) $state->ease_factor <= 1.6
        );

        return [
            'reviewed_count' => $states->count(),
            'due_count' => $due->count(),
            'upcoming_count' => $upcoming->count(),
            'weak_count' => $weak->count(),
            'next_due_at' => $states
                ->pluck('next_review_at')
                ->filter()
                ->sort()
                ->first(),
            'weak_lessons' => $weak
                ->map(fn (FlashcardCardState $state) => $state->minnaSection?->lesson)
                ->filter()
                ->unique('id')
                ->sortBy('number')
                ->take(5)
                ->values(),
            'weak_cards' => $this->formatWeakCards($weak->sortBy('ease_factor')->take(12)),
        ];
    }

    /** @deprecated Dung getFlashcardsByLessons([$number]) thay the */
    public function getFlashcardsByLesson(int $number): array
    {
        $r = $this->getFlashcardsByLessons([$number], false);

        return [
            'lesson' => $r['lessons'][0] ?? null,
            'cards' => $r['cards'],
        ];
    }

    public function getFavoriteFlashcards(User $user): array
    {
        $cards = FavoriteItem::query()
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(fn (FavoriteItem $item) => [
                'front' => $item->front,
                'back' => $item->back,
                'lesson_number' => $item->lesson_number,
                'favorite_id' => $item->id,
            ])
            ->values()
            ->all();

        return [
            'lessons' => [],
            'cards' => $cards,
        ];
    }

    /** Tổng số từ vựng (thẻ) của các bài theo lesson_id - dùng cho thống kê */
    public function getTotalVocabCountByLessonIds(array $lessonIds): int
    {
        if (empty($lessonIds)) {
            return 0;
        }
        $sections = MinnaSection::select('id', 'content')
            ->where('key', 'tu-vung')
            ->whereIn('lesson_id', $lessonIds)
            ->get();
        $total = 0;
        foreach ($sections as $section) {
            $total += $this->countCards($section->content ?? []);
        }

        return $total;
    }

    private function countCards(array $content): int
    {
        $n = 0;
        foreach (self::VOCAB_KEYS as $key) {
            $items = $content[$key] ?? [];
            if (is_array($items)) {
                foreach ($items as $i) {
                    $f = $i['tu_vung'] ?? $i['jp'] ?? null;
                    if (! empty($f) && ! empty($i['nghia'] ?? null)) {
                        $n++;
                    }
                }
            }
        }

        return $n;
    }

    private function formatWeakCards(Collection $states): Collection
    {
        $sections = $states
            ->map(fn (FlashcardCardState $state) => $state->minnaSection)
            ->filter()
            ->unique('id')
            ->keyBy('id');

        return $states->map(function (FlashcardCardState $state) use ($sections) {
            $section = $sections->get($state->minna_section_id);
            if (! $section) {
                return null;
            }

            $cards = $this->extractCards((int) $section->id, $section->content ?? []);
            $card = $cards[(int) $state->card_index] ?? null;
            if (! $card) {
                return null;
            }

            return [
                'front' => $card['front'],
                'back' => $card['back'],
                'lesson_number' => $section->lesson?->number,
                'lesson_title' => $section->lesson?->title,
                'last_quality' => $state->last_quality,
                'ease_factor' => $state->ease_factor,
                'next_review_at' => $state->next_review_at,
            ];
        })->filter()->values();
    }

    /**
     * @return list<array{front: string, back: string, section_id: int, card_index: int}>
     */
    private function extractCards(int $sectionId, array $content): array
    {
        $cards = [];
        $idx = 0;
        foreach (self::VOCAB_KEYS as $key) {
            foreach ($content[$key] ?? [] as $item) {
                $front = $item['tu_vung'] ?? $item['jp'] ?? null;
                $nghia = $item['nghia'] ?? null;
                if (empty($front) || empty($nghia)) {
                    continue;
                }

                $parts = [$nghia];
                if (! empty($item['han_tu']) && $item['han_tu'] !== '-') {
                    $parts[] = '漢字: '.$item['han_tu'];
                }
                if (! empty($item['am_han']) && $item['am_han'] !== '-') {
                    $parts[] = 'Âm Hán: '.$item['am_han'];
                }
                if ($lt = $item['loai_tu'] ?? null) {
                    $parts[] = self::LOAI_TU[$lt] ?? $lt;
                }
                if (! empty($item['ghi_chu'])) {
                    $parts[] = 'Ghi chú: '.$item['ghi_chu'];
                }

                $cards[] = [
                    'front' => $front,
                    'back' => implode(' • ', $parts),
                    'section_id' => $sectionId,
                    'card_index' => $idx,
                ];
                $idx++;
            }
        }

        return $cards;
    }
}
