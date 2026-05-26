<?php

namespace App\Http\Controllers;

use App\Services\AlphabetService;
use Illuminate\Http\Request;

class UserAlphabetController extends Controller
{
    public function __construct(
        private AlphabetService $alphabetService
    ) {}

    public function index()
    {
        // Lấy alphabets
        $alphabets = $this->alphabetService->getAlphabetsByTypes(['hiragana', 'katakana', 'romaji']);
        $groupedAlphabets = $this->alphabetService->groupAlphabetsByType($alphabets);
        
        // Lấy kanjis
        $kanjis = $this->alphabetService->getKanjisByLevels(['N5', 'N4', 'N3']);
        $groupedKanjis = $this->alphabetService->groupKanjisByLevel($kanjis);
        
        return view('user.alphabet.alphabet', [
            'hiragana' => $groupedAlphabets['hiragana'],
            'katakana' => $groupedAlphabets['katakana'],
            'romaji' => $groupedAlphabets['romaji'],
            'kanjiN5' => $groupedKanjis['N5'],
            'kanjiN4' => $groupedKanjis['N4'],
            'kanjiN3' => $groupedKanjis['N3'],
        ]);
    }
}