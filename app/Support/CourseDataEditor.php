<?php

namespace App\Support;

use App\Models\N5CourseData;

final class CourseDataEditor
{
    /** Form có sẵn cho cặp section_type + section_key (không dùng JSON thô). */
    public static function hasStructured(?string $sectionType, ?string $sectionKey): bool
    {
        if ($sectionKey === 'tuVung' && $sectionType !== 'marugoto_n5') {
            return true;
        }
        if ($sectionType === 'luyen_doc') {
            return true;
        }
        if ($sectionKey === 'nguPhap' && $sectionType !== 'marugoto_n5') {
            return true;
        }
        if (in_array($sectionKey, ['docHieu', 'ngheHieu'], true)) {
            return true;
        }
        if ($sectionType === 'marugoto_n5') {
            return true;
        }

        return false;
    }

    /** URL trang học N5 tương ứng (nếu có). */
    public static function learnerPreviewUrl(N5CourseData $row): ?string
    {
        $level = 'N5';

        if ($row->section_type === 'luyen_doc') {
            return route('course.luyen-doc.detail', ['level' => $level, 'id' => $row->id]);
        }
        if ($row->section_type === 'marugoto_n5') {
            return route('course.marugoto.detail', ['level' => $level, 'id' => $row->id]);
        }
        if ($row->section_type === 'speed_master_n5' && ($row->bai ?? '') !== '') {
            return route('course.speed-master.detail', ['level' => $level, 'bai' => $row->bai]);
        }

        return null;
    }

    public static function contentIsEmpty(?array $content): bool
    {
        if ($content === null || $content === []) {
            return true;
        }

        return ! self::hasNonEmptyLeaf($content);
    }

    private static function hasNonEmptyLeaf(mixed $value): bool
    {
        if (is_array($value)) {
            foreach ($value as $item) {
                if (self::hasNonEmptyLeaf($item)) {
                    return true;
                }
            }

            return false;
        }

        if ($value === null || $value === '') {
            return false;
        }

        return true;
    }
}
