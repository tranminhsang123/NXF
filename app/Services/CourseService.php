<?php

namespace App\Services;

use App\Models\N5CourseData;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;

class CourseService
{
    private const VALID_LEVELS = ['N5', 'N4', 'N3', 'N2', 'N1'];
    private const VALID_SECTION_TYPES = ['speed_master_n5', 'luyen_doc', 'marugoto_n5'];
    private const CACHE_TTL = 600;

    /**
     * Validate và normalize level
     */
    public function validateLevel(string $level): string
    {
        $level = strtoupper($level);
        
        if (!in_array($level, self::VALID_LEVELS)) {
            throw new InvalidArgumentException('Khóa học không tồn tại');
        }
        
        return $level;
    }

    /**
     * Validate section type
     */
    public function validateSectionType(string $sectionType): void
    {
        if (!in_array($sectionType, self::VALID_SECTION_TYPES)) {
            throw new InvalidArgumentException('Phần học không tồn tại');
        }
    }

    /**
     * Lấy metadata khóa học theo level
     */
    public function getCourseMetadata(string $level): ?array
    {
        $courses = [
            'N5' => [
                'title' => 'N5 - Sơ cấp (Beginner)',
                'subtitle' => 'Khóa học dành cho người mới bắt đầu',
                'icon' => '🌱',
                'color' => 'red',
                'bgColor' => 'bg-red-50',
                'borderColor' => 'border-red-200',
                'buttonColor' => 'bg-red-600 hover:bg-red-700',
                'textColor' => 'text-red-600',
                'description' => 'Khóa học N5 phù hợp cho người mới bắt đầu học tiếng Nhật. Bạn sẽ được học từ những kiến thức cơ bản nhất như bảng chữ cái, cách đọc, cách viết, và những câu giao tiếp đơn giản trong cuộc sống hàng ngày.',
            ],
            'N4' => [
                'title' => 'N4 - Sơ trung cấp (Elementary)',
                'subtitle' => 'Khóa học nâng cao từ N5',
                'icon' => '🌿',
                'color' => 'orange',
                'bgColor' => 'bg-orange-50',
                'borderColor' => 'border-orange-200',
                'buttonColor' => 'bg-orange-600 hover:bg-orange-700',
                'textColor' => 'text-orange-600',
                'description' => 'Khóa học N4 giúp bạn nâng cao kiến thức từ N5, học thêm nhiều từ vựng và ngữ pháp phức tạp hơn.',
            ],
            'N3' => [
                'title' => 'N3 - Trung cấp (Intermediate)',
                'subtitle' => 'Khóa học trung cấp',
                'icon' => '🌳',
                'color' => 'yellow',
                'bgColor' => 'bg-yellow-50',
                'borderColor' => 'border-yellow-200',
                'buttonColor' => 'bg-yellow-600 hover:bg-yellow-700',
                'textColor' => 'text-yellow-600',
                'description' => 'Khóa học N3 dành cho người đã có nền tảng vững chắc, muốn nâng cao trình độ tiếng Nhật.',
            ],
            'N2' => [
                'title' => 'N2 - Trung cao cấp (Upper Intermediate)',
                'subtitle' => 'Khóa học trung cao cấp',
                'icon' => '🏔️',
                'color' => 'blue',
                'bgColor' => 'bg-blue-50',
                'borderColor' => 'border-blue-200',
                'buttonColor' => 'bg-blue-600 hover:bg-blue-700',
                'textColor' => 'text-blue-600',
                'description' => 'Khóa học N2 giúp bạn đạt trình độ trung cao cấp, có thể giao tiếp và làm việc bằng tiếng Nhật.',
            ],
            'N1' => [
                'title' => 'N1 - Cao cấp (Advanced)',
                'subtitle' => 'Khóa học cao cấp',
                'icon' => '🏆',
                'color' => 'purple',
                'bgColor' => 'bg-purple-50',
                'borderColor' => 'border-purple-200',
                'buttonColor' => 'bg-purple-600 hover:bg-purple-700',
                'textColor' => 'text-purple-600',
                'description' => 'Khóa học N1 là cấp độ cao nhất, dành cho người muốn thành thạo tiếng Nhật như người bản xứ.',
            ],
        ];

        return $courses[$level] ?? null;
    }

    /**
     * Lấy danh sách sections cho N5 từ database (có cache)
     */
    public function getN5Sections(): array
    {
        return Cache::remember('course:n5:sections', self::CACHE_TTL, function () {
            $sections = [];

            $speedMasterCount = N5CourseData::where('section_type', 'speed_master_n5')->published()->count();
            if ($speedMasterCount > 0) {
                $sections[] = [
                    'title' => 'Speed Master N5',
                    'description' => 'Giáo trình Speed Master N5 - Học nhanh và hiệu quả',
                    'icon' => '⚡',
                    'type' => 'speed_master_n5'
                ];
            }

            $luyenDocCount = N5CourseData::where('section_type', 'luyen_doc')->published()->count();
            if ($luyenDocCount > 0) {
                $sections[] = [
                    'title' => 'Luyện đọc',
                    'description' => 'Rèn luyện kỹ năng đọc hiểu qua các bài đọc đa dạng',
                    'icon' => '📖',
                    'type' => 'luyen_doc'
                ];
            }

            $marugotoCount = N5CourseData::where('section_type', 'marugoto_n5')->published()->count();
            if ($marugotoCount > 0) {
                $sections[] = [
                    'title' => 'Marugoto N5',
                    'description' => 'Giáo trình Marugoto N5 - Học tiếng Nhật giao tiếp thực tế',
                    'icon' => '🇯🇵',
                    'type' => 'marugoto_n5'
                ];
            }

            $sections[] = [
                'title' => 'Korede Daijoubu N4 & N5',
                'description' => 'Sách luyện thi Korede Daijoubu - Chuẩn bị cho kỳ thi JLPT',
                'icon' => '📚',
                'type' => null,
                'disabled' => true
            ];
            $sections[] = [
                'title' => 'Gokaku Dekiru N4 & N5',
                'description' => 'Sách luyện thi Gokaku Dekiru - Luyện đề thi thử mới nhất',
                'icon' => '✅',
                'type' => null,
                'disabled' => true
            ];
            $sections[] = [
                'title' => 'Tanki Master N5',
                'description' => 'Sách luyện thi Tanki Master N5 - Tổng hợp kiến thức và đề thi',
                'icon' => '🎯',
                'type' => null,
                'disabled' => true
            ];

            return $sections;
        });
    }

    /**
     * Lấy danh sách bài luyện đọc (có cache)
     */
    public function getLuyenDocLessons()
    {
        return Cache::remember('course:luyen_doc:lessons', self::CACHE_TTL, function () {
            $lessons = N5CourseData::where('section_type', 'luyen_doc')
                ->published()
                ->select('id', 'bai', 'title', 'order')
                ->orderBy('order')
                ->get();

            if ($lessons->isEmpty()) {
                throw new InvalidArgumentException('Chưa có dữ liệu luyện đọc');
            }

            return $lessons;
        });
    }

    /**
     * Lấy chi tiết bài luyện đọc (có cache theo id)
     */
    public function getLuyenDocDetail(int $id): N5CourseData
    {
        return Cache::remember("course:luyen_doc:detail:{$id}", self::CACHE_TTL, function () use ($id) {
            $item = N5CourseData::where('section_type', 'luyen_doc')
                ->published()
                ->where('id', $id)
                ->first();

            if (!$item) {
                throw new InvalidArgumentException('Không tìm thấy bài luyện đọc');
            }

            return $item;
        });
    }

    /**
     * Lấy danh sách bài Marugoto N5 (có cache)
     */
    public function getMarugotoLessons()
    {
        return Cache::remember('course:marugoto_n5:lessons', self::CACHE_TTL, function () {
            $lessons = N5CourseData::where('section_type', 'marugoto_n5')
                ->published()
                ->select('id', 'bai', 'title', 'order')
                ->orderBy('order')
                ->get();

            if ($lessons->isEmpty()) {
                throw new InvalidArgumentException('Chưa có dữ liệu Marugoto N5');
            }

            return $lessons;
        });
    }

    /**
     * Lấy chi tiết bài Marugoto N5 (có cache theo id)
     */
    public function getMarugotoDetail(int $id): N5CourseData
    {
        return Cache::remember("course:marugoto_n5:detail:{$id}", self::CACHE_TTL, function () use ($id) {
            $item = N5CourseData::where('section_type', 'marugoto_n5')
                ->published()
                ->where('id', $id)
                ->first();

            if (!$item) {
                throw new InvalidArgumentException('Không tìm thấy bài Marugoto N5');
            }

            return $item;
        });
    }

    /**
     * Lấy danh sách bài Speed Master N5 (có cache)
     */
    public function getSpeedMasterLessons(): array
    {
        return Cache::remember('course:speed_master_n5:lessons', self::CACHE_TTL, function () {
            $allLessons = N5CourseData::where('section_type', 'speed_master_n5')
                ->published()
                ->where('section_key', 'tuVung')
                ->select('bai', 'title', 'order')
                ->orderBy('order')
                ->get();

            if ($allLessons->isEmpty()) {
                throw new InvalidArgumentException('Chưa có dữ liệu Speed Master N5');
            }

            return $allLessons->map(function ($lesson) {
                return [
                    'bai' => $lesson->bai,
                    'title' => $lesson->title,
                ];
            })->toArray();
        });
    }

    /**
     * Lấy chi tiết bài Speed Master N5 (có cache theo bai)
     */
    public function getSpeedMasterDetail(string $bai): array
    {
        return Cache::remember("course:speed_master_n5:detail:{$bai}", self::CACHE_TTL, function () use ($bai) {
            $allData = N5CourseData::where('section_type', 'speed_master_n5')
                ->published()
                ->where('bai', $bai)
                ->orderBy('order')
                ->get();

            if ($allData->isEmpty()) {
                throw new InvalidArgumentException('Không tìm thấy bài học');
            }

            $groupedData = $allData->groupBy('section_key');
            $title = $allData->first()->title ?? '';

            return [
                'groupedData' => $groupedData,
                'title' => $title,
            ];
        });
    }

    /**
     * Lấy dữ liệu section theo type (có cache)
     */
    public function getSectionData(string $sectionType): array
    {
        return Cache::remember("course:section_data:{$sectionType}", self::CACHE_TTL, function () use ($sectionType) {
            $data = N5CourseData::where('section_type', $sectionType)
                ->published()
                ->orderBy('order')
                ->get();

            if ($data->isEmpty()) {
                throw new InvalidArgumentException('Chưa có dữ liệu cho phần học này');
            }

            $groupedData = $data->groupBy('section_key');

            return [
                'data' => $data,
                'groupedData' => $groupedData,
            ];
        });
    }

    /**
     * Kiểm tra level có hỗ trợ dữ liệu không
     */
    public function hasDataForLevel(string $level): bool
    {
        return $level === 'N5';
    }
}
