<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogoSetting;
use App\Models\SocialLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class LogoSettingController extends Controller
{
    public function index(): View
    {
        $setting = LogoSetting::query()->latest('id')->first();

        return view('admin.logo-settings.index', [
            'setting' => $setting,
            'currentLogoUrl' => LogoSetting::currentLogoUrl(),
            'socialLinks' => SocialLink::query()->ordered()->get(),
            'socialPlatforms' => SocialLink::platformLabels(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'logo_title' => ['nullable', 'string', 'max:60'],
            'logo_subtitle' => ['nullable', 'string', 'max:120'],
        ]);

        $path = isset($validated['logo']) ? $validated['logo']->store('logos', 'public') : null;

        LogoSetting::query()->create([
            'logo_path' => $path,
            'logo_title' => $validated['logo_title'] ?? null,
            'logo_subtitle' => $validated['logo_subtitle'] ?? null,
        ]);

        return redirect()
            ->route('admin.logo-settings.index')
            ->with('success', 'Đã lưu cài đặt logo.');
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'logo_title' => ['nullable', 'string', 'max:60'],
            'logo_subtitle' => ['nullable', 'string', 'max:120'],
        ]);

        $setting = LogoSetting::query()->latest('id')->firstOrFail();
        $oldPath = $setting->logo_path;
        $newPath = $setting->logo_path;

        if (isset($validated['logo'])) {
            $newPath = $validated['logo']->store('logos', 'public');
        }

        $setting->update([
            'logo_path' => $newPath,
            'logo_title' => $validated['logo_title'] ?? null,
            'logo_subtitle' => $validated['logo_subtitle'] ?? null,
        ]);

        if (isset($validated['logo']) && $oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        return redirect()
            ->route('admin.logo-settings.index')
            ->with('success', 'Đã cập nhật cài đặt logo.');
    }

    public function destroy(): RedirectResponse
    {
        $setting = LogoSetting::query()->latest('id')->first();

        if (! $setting) {
            return redirect()
                ->route('admin.logo-settings.index')
                ->with('success', 'Đã về logo mặc định.');
        }

        if ($setting->logo_path) {
            Storage::disk('public')->delete($setting->logo_path);
        }

        $setting->delete();

        return redirect()
            ->route('admin.logo-settings.index')
            ->with('success', 'Đã xóa logo tùy chỉnh, quay về mặc định.');
    }
}
