<?php

namespace App\Http\Controllers;

use App\Services\KanjiService;
use Illuminate\Http\Request;

class UserKanjiController extends Controller
{
    public function __construct(
        private KanjiService $kanjiService
    ) {}

    /**
     * Chọn cấp độ Kanji (N5–N1)
     */
    public function index()
    {
        $countsByLevel = $this->kanjiService->getCountsByLevel();
        return view('user.kanji.index', compact('countsByLevel'));
    }

    /**
     * Danh sách Kanji theo level
     */
    public function list(string $level)
    {
        if (!in_array($level, KanjiService::LEVELS, true)) {
            abort(404, 'Cấp độ không hợp lệ.');
        }
        $kanjis = $this->kanjiService->getByLevel($level);
        return view('user.kanji.list', compact('level', 'kanjis'));
    }

    /**
     * Flashcard Kanji theo level
     */
    public function flashcard(string $level)
    {
        if (!in_array($level, KanjiService::LEVELS, true)) {
            abort(404, 'Cấp độ không hợp lệ.');
        }
        $kanjis = $this->kanjiService->getByLevel($level);
        $cards = $kanjis->map(fn ($k) => [
            'front' => $k->character,
            'back' => implode(' • ', array_filter([
                $k->meaning,
                $k->on_reading ? 'On: ' . $k->on_reading : null,
                $k->kun_reading ? 'Kun: ' . $k->kun_reading : null,
                $k->stroke_count ? $k->stroke_count . ' nét' : null,
            ])),
        ])->all();
        return view('user.kanji.flashcard', compact('level', 'cards'));
    }
}
