<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AdminAuditLog::query()
            ->with('actor:id,name,email')
            ->latest('created_at');

        if ($request->filled('action')) {
            $query->where('action', (string) $request->query('action'));
        }
        if ($request->filled('actor_id')) {
            $query->where('actor_id', (int) $request->query('actor_id'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->query('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->query('date_to'));
        }

        $logs = $query->paginate(25)->withQueryString();
        $actions = AdminAuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action');

        return view('admin.audit-logs.index', compact('logs', 'actions'));
    }
}
