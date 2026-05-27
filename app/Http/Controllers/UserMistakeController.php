<?php

namespace App\Http\Controllers;

use App\Services\UserMistakeService;
use Illuminate\Http\Request;

class UserMistakeController extends Controller
{
    public function __construct(
        private UserMistakeService $mistakeService,
    ) {}

    public function index(Request $request)
    {
        return view('user.mistakes', array_merge(
            ['user' => $request->user()],
            $this->mistakeService->build($request->user())
        ));
    }
}
