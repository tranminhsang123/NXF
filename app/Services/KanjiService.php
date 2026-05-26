<?php

namespace App\Services;

use App\Models\Kanji;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class KanjiService
{
    /** Các level hỗ trợ ôn (N5 → N1) */
    public const LEVELS = ['N5', 'N4', 'N3', 'N2', 'N1'];

    private const CACHE_TTL = 600;

    /**
     * Số lượng Kanji theo từng level (có cache)
     */
    public function getCountsByLevel(): Collection
    {
        return Cache::remember('kanji:counts_by_level', self::CACHE_TTL, function () {
            return Kanji::query()
                ->published()
                ->selectRaw('level, COUNT(*) as count')
                ->whereIn('level', self::LEVELS)
                ->groupBy('level')
                ->orderByRaw("FIELD(level, 'N5', 'N4', 'N3', 'N2', 'N1')")
                ->pluck('count', 'level');
        });
    }

    /**
     * Lấy danh sách Kanji theo level (có cache theo level)
     */
    public function getByLevel(string $level)
    {
        if (!in_array($level, self::LEVELS, true)) {
            return collect();
        }
        return Cache::remember("kanji:by_level:{$level}", self::CACHE_TTL, function () use ($level) {
            return Kanji::byLevel($level)
                ->published()
                ->orderBy('character')
                ->get(['id', 'character', 'meaning', 'on_reading', 'kun_reading', 'level', 'stroke_count', 'radical', 'examples']);
        });
    }

    /**
     * Lấy kanjis với filter và search
     */
    public function getKanjisWithFilters(?string $level = null, ?string $search = null)
    {
        $query = Kanji::query();

        if ($level) {
            $query->byLevel($level);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('character', 'like', '%' . $search . '%')
                  ->orWhere('meaning', 'like', '%' . $search . '%')
                  ->orWhere('on_reading', 'like', '%' . $search . '%')
                  ->orWhere('kun_reading', 'like', '%' . $search . '%');
            });
        }

        return $query->orderBy('level')->orderBy('character');
    }
}
