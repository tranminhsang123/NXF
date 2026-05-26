<?php

namespace App\Http\Controllers;

use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Services\FlashcardService;

class VocabularyController extends Controller
{
    public function __construct(
        private FlashcardService $flashcardService
    ) {}

    /**
     * Danh sách bài có từ vựng
     */
    public function index()
    {
        $lessonsWithCount = $this->flashcardService->getLessonsWithVocabCount();
        return view('vocabulary.index', compact('lessonsWithCount'));
    }

    /**
     * Từ vựng theo bài
     */
    public function show(int $number)
    {
        $lesson = MinnaLesson::where('number', $number)->firstOrFail();
        $section = MinnaSection::where('lesson_id', $lesson->id)
            ->where('key', 'tu-vung')
            ->first();

        $vocabGroups = [];
        if ($section?->content && is_array($section->content)) {
            $labels = [
                'vocab' => 'Từ vựng',
                'mau_cau' => 'Mẫu câu',
                'countries' => 'Tên nước',
                'proper_nouns' => 'Danh từ riêng',
                'cau' => 'Câu',
                'places' => 'Địa danh',
                'rail' => 'Từ vựng tàu',
            ];
            foreach ($labels as $key => $label) {
                $items = $section->content[$key] ?? [];
                if (is_array($items) && !empty($items)) {
                    $vocabGroups[$label] = $items;
                }
            }
        }

        return view('vocabulary.show', compact('lesson', 'vocabGroups'));
    }
}
