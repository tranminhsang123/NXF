<?php

namespace App\Services;

use App\Models\Alphabet;
use App\Models\Kanji;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\N5CourseData;
use Illuminate\Database\Eloquent\Model;

class ContentValidationService
{
    public function validate(Model $model): array
    {
        return match (true) {
            $model instanceof Alphabet => $this->validateAlphabet($model),
            $model instanceof Kanji => $this->validateKanji($model),
            $model instanceof MinnaLesson => $this->validateMinnaLesson($model),
            $model instanceof MinnaSection => $this->validateMinnaSection($model),
            $model instanceof N5CourseData => $this->validateCourseData($model),
            default => ['Loại nội dung không hợp lệ.'],
        };
    }

    private function validateAlphabet(Alphabet $item): array
    {
        return array_values(array_filter([
            trim((string) $item->character) === '' ? 'Thiếu ký tự.' : null,
            trim((string) $item->romaji) === '' ? 'Thiếu romaji.' : null,
            ! in_array($item->type, ['hiragana', 'katakana', 'romaji'], true) ? 'Loại bảng chữ cái không hợp lệ.' : null,
        ]));
    }

    private function validateKanji(Kanji $item): array
    {
        return array_values(array_filter([
            trim((string) $item->character) === '' ? 'Thiếu ký tự.' : null,
            trim((string) $item->meaning) === '' ? 'Thiếu nghĩa.' : null,
            trim((string) $item->level) === '' ? 'Thiếu cấp JLPT.' : null,
            (int) $item->stroke_count < 1 ? 'Số nét phải lớn hơn 0.' : null,
        ]));
    }

    private function validateMinnaLesson(MinnaLesson $item): array
    {
        return array_values(array_filter([
            (int) $item->number < 1 ? 'Số bài phải lớn hơn 0.' : null,
            trim((string) $item->title) === '' ? 'Thiếu tiêu đề.' : null,
            $item->sections()->count() === 0 ? 'Bài học chưa có phần nội dung.' : null,
        ]));
    }

    private function validateMinnaSection(MinnaSection $item): array
    {
        $content = $item->content ?? [];

        return array_values(array_filter([
            trim((string) $item->key) === '' ? 'Thiếu mã phần.' : null,
            trim((string) $item->title) === '' ? 'Thiếu tiêu đề phần.' : null,
            ! is_array($content) || count($content) === 0 ? 'Nội dung phần học đang trống.' : null,
        ]));
    }

    private function validateCourseData(N5CourseData $item): array
    {
        $content = $item->content ?? [];

        return array_values(array_filter([
            trim((string) $item->section_type) === '' ? 'Thiếu loại phần học.' : null,
            trim((string) $item->title) === '' ? 'Thiếu tiêu đề.' : null,
            ! is_array($content) || count($content) === 0 ? 'Nội dung khóa học đang trống.' : null,
        ]));
    }
}
