<?php

namespace App\Services;

use App\Models\Alphabet;
use App\Models\Kanji;
use Illuminate\Support\Facades\Cache;

class AlphabetService
{
    private const CACHE_TTL = 600; // 10 phút

    /**
     * Lấy tất cả alphabets theo type (có cache cho trang user /alphabet)
     */
    public function getAlphabetsByTypes(array $types)
    {
        $t = $types;
        sort($t);
        $key = 'alphabet:by_types:' . implode(',', $t);
        return Cache::remember($key, self::CACHE_TTL, function () use ($types) {
            return Alphabet::whereIn('type', $types)
                ->published()
                ->select('id', 'character', 'romaji', 'type', 'category')
                ->orderBy('type')
                ->orderBy('character')
                ->get();
        });
    }

    /**
     * Chia alphabets theo type
     */
    public function groupAlphabetsByType($alphabets)
    {
        return [
            'hiragana' => $alphabets->where('type', 'hiragana')->values(),
            'katakana' => $alphabets->where('type', 'katakana')->values(),
            'romaji' => $alphabets->where('type', 'romaji')->values(),
        ];
    }

    /**
     * Lấy tất cả kanjis theo levels (có cache cho trang user /alphabet)
     */
    public function getKanjisByLevels(array $levels)
    {
        $l = $levels;
        sort($l);
        $key = 'alphabet:kanjis_by_levels:' . implode(',', $l);
        return Cache::remember($key, self::CACHE_TTL, function () use ($levels) {
            return Kanji::whereIn('level', $levels)
                ->published()
                ->select('id', 'character', 'meaning', 'on_reading', 'kun_reading', 'level', 'stroke_count', 'radical', 'examples')
                ->orderBy('level')
                ->orderBy('character')
                ->get();
        });
    }

    public static function clearAlphabetCache(): void
    {
        Cache::forget('alphabet:by_types:hiragana,katakana,romaji');
        Cache::forget('alphabet:kanjis_by_levels:N3,N4,N5');
    }

    /**
     * Chia kanjis theo level
     */
    public function groupKanjisByLevel($kanjis)
    {
        return [
            'N5' => $kanjis->where('level', 'N5')->values(),
            'N4' => $kanjis->where('level', 'N4')->values(),
            'N3' => $kanjis->where('level', 'N3')->values(),
        ];
    }

    /**
     * Lấy alphabets với filter và search
     */
    public function getAlphabetsWithFilters(?string $type = null, ?string $search = null)
    {
        $query = Alphabet::query();

        if ($type) {
            $query->byType($type);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('character', 'like', '%' . $search . '%')
                  ->orWhere('romaji', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('type')->orderBy('character');
    }
}
