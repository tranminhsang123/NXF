<?php

namespace App\Http\Controllers;

use App\Models\ContentErrorReport;
use Illuminate\Http\Request;

class ContentErrorReportController extends Controller
{
    public function store(Request $request)
    {
        $categories = implode(',', array_keys(ContentErrorReport::categoryLabels()));

        $data = $request->validate([
            'category' => ['required', 'in:'.$categories],
            'description' => ['required', 'string', 'max:2000'],
            'selected_text' => ['nullable', 'string', 'max:1000'],
            'content_type' => ['nullable', 'string', 'max:64'],
            'content_id' => ['nullable', 'integer', 'min:1'],
            'content_title' => ['nullable', 'string', 'max:255'],
            'page_url' => ['nullable', 'string', 'max:1000'],
            'browser_context' => ['nullable', 'array'],
        ]);

        $report = ContentErrorReport::query()->create([
            'user_id' => $request->user()->id,
            'content_type' => $data['content_type'] ?? null,
            'content_id' => $data['content_id'] ?? null,
            'content_title' => $data['content_title'] ?? null,
            'category' => $data['category'],
            'status' => ContentErrorReport::STATUS_PENDING,
            'page_url' => $data['page_url'] ?? url()->previous(),
            'selected_text' => $data['selected_text'] ?? null,
            'description' => $data['description'],
            'browser_context' => array_merge($data['browser_context'] ?? [], [
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip(),
            ]),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Đã gửi báo lỗi nội dung cho admin.',
                'report_id' => $report->id,
            ]);
        }

        return back()->with('success', 'Đã gửi báo lỗi nội dung cho admin.');
    }
}
