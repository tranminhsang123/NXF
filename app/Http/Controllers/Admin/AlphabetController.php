<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alphabet;
use App\Services\AlphabetService;
use App\Http\Requests\StoreAlphabetRequest;
use App\Http\Requests\UpdateAlphabetRequest;
use Illuminate\Http\Request;

class AlphabetController extends Controller
{
    use PerPageTrait;

    public function __construct(
        private AlphabetService $alphabetService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $this->alphabetService->getAlphabetsWithFilters(
            $request->get('type'),
            $request->get('search')
        );

        $alphabets = $query->paginate($this->adminPerPage($request))->withQueryString();

        return view('admin.alphabets.index', compact('alphabets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.alphabets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAlphabetRequest $request)
    {
        Alphabet::create($request->validated());

        return redirect()->route('admin.alphabets.index')
                        ->with('success', 'Ký tự đã được thêm thành công!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Alphabet $alphabet)
    {
        return view('admin.alphabets.edit', compact('alphabet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAlphabetRequest $request, Alphabet $alphabet)
    {
        $alphabet->update($request->validated());

        return redirect()->route('admin.alphabets.index')
                        ->with('success', 'Ký tự đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Alphabet $alphabet)
    {
        $alphabet->delete();

        return redirect()->route('admin.alphabets.index')
                        ->with('success', 'Ký tự đã được xóa thành công!');
    }
}
