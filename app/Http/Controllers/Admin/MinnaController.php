<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMinnaRequest;
use App\Http\Requests\UpdateMinnaRequest;
use App\Models\MinnaLesson;
use App\Models\MinnaSection;
use Illuminate\Http\Request;

class MinnaController extends Controller
{
    use PerPageTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = MinnaLesson::query();

        // Search by title or number
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('number', 'like', '%' . $request->search . '%');
            });
        }

        $lessons = $query->withCount('sections')->orderBy('number')->paginate($this->adminPerPage($request))->withQueryString();

        return view('admin.minna.index', compact('lessons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.minna.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMinnaRequest $request)
    {
        $lesson = MinnaLesson::create($request->validated());

        $this->createDefaultSections($lesson);

        return redirect()->route('admin.minna.index')
                        ->with('success', 'Bài học đã được thêm thành công! Đã tạo 5 phần (Từ vựng, Ngữ pháp, Luyện đọc, Hội thoại, Hán tự).');
    }

    /**
     * Display the specified resource.
     */
    public function show(MinnaLesson $minna)
    {
        $minna->load('sections');
        return view('admin.minna.show', compact('minna'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MinnaLesson $minna)
    {
        return view('admin.minna.edit', compact('minna'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMinnaRequest $request, MinnaLesson $minna)
    {
        $minna->update($request->validated());

        return redirect()->route('admin.minna.index')
                        ->with('success', 'Bài học đã được cập nhật thành công!');
    }

    /**
     * Tạo 5 phần mặc định cho bài học (khi bài chưa có phần)
     */
    public function addSections(MinnaLesson $minna)
    {
        if ($minna->sections()->count() > 0) {
            return redirect()->route('admin.minna.show', $minna)
                ->with('info', 'Bài học đã có phần. Không cần tạo thêm.');
        }

        $this->createDefaultSections($minna);

        return redirect()->route('admin.minna.show', $minna)
            ->with('success', 'Đã tạo 5 phần: Từ vựng, Ngữ pháp, Luyện đọc, Hội thoại, Hán tự.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MinnaLesson $minna)
    {
        $minna->delete();

        return redirect()->route('admin.minna.index')
                        ->with('success', 'Bài học đã được xóa thành công!');
    }

    /**
     * Tạo 5 phần mặc định cho bài học (Từ vựng, Ngữ pháp, Luyện đọc, Hội thoại, Hán tự)
     */
    private function createDefaultSections(MinnaLesson $lesson): void
    {
        $sectionCatalog = [
            ['key' => 'tu-vung', 'title' => 'Phần 1: Từ vựng'],
            ['key' => 'ngu-phap', 'title' => 'Phần 2: Ngữ pháp'],
            ['key' => 'luyen-doc', 'title' => 'Phần 3: Luyện đọc'],
            ['key' => 'hoi-thoai', 'title' => 'Phần 4: Hội thoại'],
            ['key' => 'han-tu', 'title' => 'Phần 5: Hán tự'],
        ];

        foreach ($sectionCatalog as $index => $sectionDef) {
            MinnaSection::create([
                'lesson_id' => $lesson->id,
                'order_index' => $index + 1,
                'key' => $sectionDef['key'],
                'title' => $sectionDef['title'],
                'content' => null,
                'media_url' => null,
            ]);
        }
    }
}
