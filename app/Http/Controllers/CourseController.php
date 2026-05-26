<?php

namespace App\Http\Controllers;

use App\Services\CourseService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class CourseController extends Controller
{
    public function __construct(
        private CourseService $courseService
    ) {}

    /**
     * Hiển thị trang tổng hợp các khóa học N5-N1
     */
    public function index()
    {
        return view('course.index');
    }

    /**
     * Hiển thị thông tin khóa học theo level JLPT
     */
    public function show($level)
    {
        try {
            $level = $this->courseService->validateLevel($level);
            $courseData = $this->courseService->getCourseMetadata($level);

            if (!$courseData) {
                abort(404, 'Khóa học không tồn tại');
            }

            // Lấy dữ liệu từ database nếu là N5
            if ($level === 'N5') {
                $courseData['sections'] = $this->courseService->getN5Sections();
            }

            return view('course.show', compact('courseData', 'level'));
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết một section của khóa học
     */
    public function showSection($level, $sectionType)
    {
        try {
            $level = $this->courseService->validateLevel($level);
            
            if (!$this->courseService->hasDataForLevel($level)) {
                abort(404, 'Khóa học chưa có dữ liệu');
            }

            $this->courseService->validateSectionType($sectionType);

            // Nếu là luyện đọc, hiển thị danh sách bài
            if ($sectionType === 'luyen_doc') {
                return $this->showLuyenDocList($level);
            }

            // Nếu là marugoto_n5, hiển thị danh sách bài
            if ($sectionType === 'marugoto_n5') {
                return $this->showMarugotoList($level);
            }

            // Nếu là speed_master_n5, hiển thị danh sách bài
            if ($sectionType === 'speed_master_n5') {
                return $this->showSpeedMasterList($level);
            }

            // Lấy dữ liệu từ database
            $result = $this->courseService->getSectionData($sectionType);
            $courseData = $this->courseService->getCourseMetadata($level);

            return view('course.section', compact('data', 'groupedData', 'sectionType', 'level', 'courseData'))
                ->with('data', $result['data'])
                ->with('groupedData', $result['groupedData']);
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách bài luyện đọc
     */
    public function showLuyenDocList($level)
    {
        try {
            $level = $this->courseService->validateLevel($level);
            $lessons = $this->courseService->getLuyenDocLessons();
            $courseData = $this->courseService->getCourseMetadata($level);

            return view('course.sections.luyen_doc_list', compact('lessons', 'level', 'courseData'));
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết một bài luyện đọc
     */
    public function showLuyenDocDetail($level, $id)
    {
        try {
            $level = $this->courseService->validateLevel($level);
            $item = $this->courseService->getLuyenDocDetail($id);
            $courseData = $this->courseService->getCourseMetadata($level);

            return view('course.sections.luyen_doc_detail', compact('item', 'level', 'courseData'));
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách bài Marugoto N5
     */
    public function showMarugotoList($level)
    {
        try {
            $level = $this->courseService->validateLevel($level);
            $lessons = $this->courseService->getMarugotoLessons();
            $courseData = $this->courseService->getCourseMetadata($level);

            return view('course.sections.marugoto_n5_list', compact('lessons', 'level', 'courseData'));
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết một bài Marugoto N5
     */
    public function showMarugotoDetail($level, $id)
    {
        try {
            $level = $this->courseService->validateLevel($level);
            $item = $this->courseService->getMarugotoDetail($id);
            $courseData = $this->courseService->getCourseMetadata($level);

            return view('course.sections.marugoto_n5_detail', compact('item', 'level', 'courseData'));
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Hiển thị danh sách bài Speed Master N5
     */
    public function showSpeedMasterList($level)
    {
        try {
            $level = $this->courseService->validateLevel($level);
            $lessons = $this->courseService->getSpeedMasterLessons();
            $courseData = $this->courseService->getCourseMetadata($level);

            return view('course.sections.speed_master_n5_list', compact('lessons', 'level', 'courseData'));
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }

    /**
     * Hiển thị chi tiết một bài Speed Master N5
     */
    public function showSpeedMasterDetail($level, $bai)
    {
        try {
            $level = $this->courseService->validateLevel($level);
            $result = $this->courseService->getSpeedMasterDetail($bai);
            $courseData = $this->courseService->getCourseMetadata($level);

            return view('course.sections.speed_master_n5_detail', [
                'groupedData' => $result['groupedData'],
                'bai' => $bai,
                'title' => $result['title'],
                'level' => $level,
                'courseData' => $courseData
            ]);
        } catch (InvalidArgumentException $e) {
            abort(404, $e->getMessage());
        }
    }
}

