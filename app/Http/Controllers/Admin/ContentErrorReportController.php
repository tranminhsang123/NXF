<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentErrorReport;
use App\Models\User;
use App\Services\AdminAuditService;
use Illuminate\Http\Request;

class ContentErrorReportController extends Controller
{
    public function index(Request $request)
    {
        $query = ContentErrorReport::query()
            ->with(['user:id,name,email', 'assignee:id,name,email', 'resolver:id,name,email'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', (string) $request->query('status'));
        }
        if ($request->filled('category')) {
            $query->where('category', (string) $request->query('category'));
        }
        if ($request->filled('q')) {
            $q = (string) $request->query('q');
            $query->where(function ($builder) use ($q) {
                $builder->where('content_title', 'like', '%'.$q.'%')
                    ->orWhere('description', 'like', '%'.$q.'%')
                    ->orWhere('selected_text', 'like', '%'.$q.'%');
            });
        }

        $reports = $query->paginate(20)->withQueryString();
        $stats = [
            'pending' => ContentErrorReport::query()->where('status', ContentErrorReport::STATUS_PENDING)->count(),
            'in_progress' => ContentErrorReport::query()->where('status', ContentErrorReport::STATUS_IN_PROGRESS)->count(),
            'resolved' => ContentErrorReport::query()->where('status', ContentErrorReport::STATUS_RESOLVED)->count(),
            'dismissed' => ContentErrorReport::query()->where('status', ContentErrorReport::STATUS_DISMISSED)->count(),
        ];

        return view('admin.content-error-reports.index', [
            'reports' => $reports,
            'stats' => $stats,
            'statuses' => ContentErrorReport::statusLabels(),
            'categories' => ContentErrorReport::categoryLabels(),
        ]);
    }

    public function show(ContentErrorReport $contentReport)
    {
        $contentReport->load(['user:id,name,email', 'assignee:id,name,email', 'resolver:id,name,email']);
        $admins = User::query()->where('role', 'admin')->orderBy('name')->get(['id', 'name', 'email']);

        return view('admin.content-error-reports.show', [
            'report' => $contentReport,
            'admins' => $admins,
            'statuses' => ContentErrorReport::statusLabels(),
        ]);
    }

    public function update(Request $request, ContentErrorReport $contentReport)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,in_progress,resolved,dismissed'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'resolution_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $before = $contentReport->only(['status', 'assigned_to', 'resolved_by', 'resolution_note', 'resolved_at']);
        $status = $data['status'];
        $isClosed = in_array($status, [
            ContentErrorReport::STATUS_RESOLVED,
            ContentErrorReport::STATUS_DISMISSED,
        ], true);

        $contentReport->update([
            'status' => $status,
            'assigned_to' => $data['assigned_to'] ?? null,
            'resolved_by' => $isClosed ? $request->user()->id : null,
            'resolution_note' => $data['resolution_note'] ?? null,
            'resolved_at' => $isClosed ? now() : null,
        ]);

        app(AdminAuditService::class)->audit(
            $request->user(),
            $contentReport,
            'content_error_report_updated',
            'Đã cập nhật báo lỗi nội dung #'.$contentReport->id,
            $before,
            $contentReport->only(['status', 'assigned_to', 'resolved_by', 'resolution_note', 'resolved_at'])
        );

        return redirect()->route('admin.content-reports.show', $contentReport)->with('success', 'Đã cập nhật báo lỗi nội dung.');
    }
}
