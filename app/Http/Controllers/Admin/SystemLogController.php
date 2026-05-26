<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Http\Request;

class SystemLogController extends Controller
{
    public function index(Request $request)
    {
        $query = SystemLog::with('user:id,name,email')->orderByDesc('created_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->paginate(25)->withQueryString();

        $types = [
            'failed_login' => 'Đăng nhập thất bại',
            'user_registered' => 'Đăng ký mới',
            'user_locked' => 'Khóa tài khoản',
            'user_unlocked' => 'Mở khóa',
            'user_auto_unlocked' => 'Tự mở khóa',
        ];

        return view('admin.system-logs.index', compact('logs', 'types'));
    }
}
