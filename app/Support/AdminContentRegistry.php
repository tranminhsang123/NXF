<?php

namespace App\Support;

use App\Models\Alphabet;
use App\Models\Kanji;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use App\Models\N5CourseData;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class AdminContentRegistry
{
    public static function types(): array
    {
        return [
            'alphabet' => [
                'class' => Alphabet::class,
                'label' => 'Bảng chữ cái',
                'title' => fn (Alphabet $item) => $item->character.' / '.$item->romaji,
            ],
            'kanji' => [
                'class' => Kanji::class,
                'label' => 'Kanji',
                'title' => fn (Kanji $item) => $item->character.' - '.$item->meaning,
            ],
            'minna_lesson' => [
                'class' => MinnaLesson::class,
                'label' => 'Bài Minna',
                'title' => fn (MinnaLesson $item) => 'Bài '.$item->number.' - '.$item->title,
            ],
            'minna_section' => [
                'class' => MinnaSection::class,
                'label' => 'Phần bài Minna',
                'title' => fn (MinnaSection $item) => $item->title,
            ],
            'course_data' => [
                'class' => N5CourseData::class,
                'label' => 'Dữ liệu khóa học',
                'title' => fn (N5CourseData $item) => trim(($item->section_type ?? '').' '.($item->bai ?? '').' '.($item->title ?? '')),
            ],
        ];
    }

    public static function classFor(string $type): string
    {
        $entry = self::types()[$type] ?? null;
        if (! $entry) {
            throw new InvalidArgumentException('Loại nội dung admin không hợp lệ.');
        }

        return $entry['class'];
    }

    public static function typeFor(Model|string $model): ?string
    {
        $class = is_string($model) ? $model : $model::class;

        foreach (self::types() as $type => $entry) {
            if ($entry['class'] === $class) {
                return $type;
            }
        }

        return null;
    }

    public static function labelFor(string|Model $typeOrModel): string
    {
        if ($typeOrModel instanceof Model) {
            $typeOrModel = self::typeFor($typeOrModel) ?? '';
        }

        return self::types()[$typeOrModel]['label'] ?? 'Nội dung';
    }

    public static function titleFor(Model $model): string
    {
        $type = self::typeFor($model);
        $entry = $type ? self::types()[$type] ?? null : null;

        if ($entry && isset($entry['title'])) {
            return (string) $entry['title']($model);
        }

        return $model::class.' #'.$model->getKey();
    }

    public static function find(string $type, int $id): Model
    {
        $class = self::classFor($type);

        return $class::query()->findOrFail($id);
    }

    public static function snapshot(Model $model): array
    {
        return $model->attributesToArray();
    }
}
