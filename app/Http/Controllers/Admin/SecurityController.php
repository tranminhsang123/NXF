<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DevtoolsViolation;
use App\Models\SecuritySetting;
use Illuminate\Http\Request;

class SecurityController extends Controller
{
    private const FEATURE_LOCK_KEYS = [
        'alphabet',
        'kanji',
        'flashcard',
        'vocabulary',
        'course',
        'minna',
    ];

    public function index()
    {
        $violations = DevtoolsViolation::with('user:id,name,email')
            ->orderByDesc('created_at')
            ->paginate(20);

        $settings = [
            'devtools_log_enabled' => SecuritySetting::getBool('devtools_log_enabled', true),
            'devtools_lock_after_violations' => SecuritySetting::getInt('devtools_lock_after_violations', 1),
            'devtools_violation_window_hours' => SecuritySetting::getInt('devtools_violation_window_hours', 24),
            'devtools_lock_message' => SecuritySetting::get('devtools_lock_message', 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.'),
            'devtools_auto_unlock_hours' => SecuritySetting::getInt('devtools_auto_unlock_hours', 0),
        ];
        $featureLocks = [];
        foreach (self::FEATURE_LOCK_KEYS as $key) {
            $featureLocks[$key] = SecuritySetting::getBool('feature_lock_' . $key, true);
        }

        return view('admin.security.index', compact('violations', 'settings', 'featureLocks'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'devtools_log_enabled' => ['nullable', 'in:0,1'],
            'devtools_lock_after_violations' => ['nullable', 'integer', 'min:0', 'max:100'],
            'devtools_violation_window_hours' => ['nullable', 'integer', 'min:1', 'max:720'],
            'devtools_lock_message' => ['nullable', 'string', 'max:500'],
            'devtools_auto_unlock_hours' => ['nullable', 'integer', 'min:0', 'max:720'],
            'feature_lock_alphabet' => ['nullable', 'in:0,1'],
            'feature_lock_kanji' => ['nullable', 'in:0,1'],
            'feature_lock_flashcard' => ['nullable', 'in:0,1'],
            'feature_lock_vocabulary' => ['nullable', 'in:0,1'],
            'feature_lock_course' => ['nullable', 'in:0,1'],
            'feature_lock_minna' => ['nullable', 'in:0,1'],
        ]);

        $hasDevtoolsPayload = $request->hasAny([
            'devtools_log_enabled',
            'devtools_lock_after_violations',
            'devtools_violation_window_hours',
            'devtools_lock_message',
            'devtools_auto_unlock_hours',
        ]);

        if ($hasDevtoolsPayload) {
            SecuritySetting::set('devtools_log_enabled', (bool) $request->input('devtools_log_enabled', 0));
            SecuritySetting::set('devtools_lock_after_violations', (int) $request->input('devtools_lock_after_violations', 1));
            SecuritySetting::set('devtools_violation_window_hours', (int) $request->input('devtools_violation_window_hours', 24));
            SecuritySetting::set('devtools_lock_message', $request->input('devtools_lock_message', ''));
            SecuritySetting::set('devtools_auto_unlock_hours', (int) $request->input('devtools_auto_unlock_hours', 0));
        }

        $hasFeaturePayload = false;
        foreach (self::FEATURE_LOCK_KEYS as $key) {
            if ($request->has('feature_lock_' . $key)) {
                $hasFeaturePayload = true;
                break;
            }
        }

        if ($hasFeaturePayload) {
            foreach (self::FEATURE_LOCK_KEYS as $key) {
                SecuritySetting::set('feature_lock_' . $key, (bool) $request->input('feature_lock_' . $key, 0));
            }
        }

        return redirect()->route('admin.security.index')
            ->with('success', 'Đã lưu cài đặt bảo mật.');
    }
}
