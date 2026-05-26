<?php

namespace App\Support\Cache;

use App\Models\N5CourseData;
use Illuminate\Support\Facades\Cache;

final class CourseN5Cache
{
    private const SECTION_TYPES = ['luyen_doc', 'marugoto_n5', 'speed_master_n5'];

    /** Xóa toàn bộ cache khóa học N5 (sau CRUD n5_course_data). */
    public static function invalidateAll(): void
    {
        $staticKeys = [
            'course:n5:sections',
            'course:luyen_doc:lessons',
            'course:marugoto_n5:lessons',
            'course:speed_master_n5:lessons',
        ];
        foreach (self::SECTION_TYPES as $t) {
            $staticKeys[] = 'course:section_data:'.$t;
        }
        foreach ($staticKeys as $key) {
            Cache::forget($key);
        }

        foreach (N5CourseData::query()->where('section_type', 'luyen_doc')->pluck('id') as $id) {
            Cache::forget('course:luyen_doc:detail:'.$id);
        }
        foreach (N5CourseData::query()->where('section_type', 'marugoto_n5')->pluck('id') as $id) {
            Cache::forget('course:marugoto_n5:detail:'.$id);
        }
        foreach (N5CourseData::query()->where('section_type', 'speed_master_n5')->pluck('bai')->unique()->filter() as $bai) {
            Cache::forget('course:speed_master_n5:detail:'.$bai);
        }

        DashboardCache::forgetAdminStats();
    }
}
