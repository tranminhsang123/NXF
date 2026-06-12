<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SocialLinkController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        SocialLink::query()->create($this->validatedSocialLink($request));

        return redirect()
            ->route('admin.logo-settings.index')
            ->with('success', 'Đã thêm liên kết mạng xã hội.');
    }

    public function update(Request $request, SocialLink $socialLink): RedirectResponse
    {
        $socialLink->update($this->validatedSocialLink($request));

        return redirect()
            ->route('admin.logo-settings.index')
            ->with('success', 'Đã cập nhật liên kết mạng xã hội.');
    }

    public function destroy(SocialLink $socialLink): RedirectResponse
    {
        $socialLink->delete();

        return redirect()
            ->route('admin.logo-settings.index')
            ->with('success', 'Đã xóa liên kết mạng xã hội.');
    }

    private function validatedSocialLink(Request $request): array
    {
        $data = $request->validate([
            'platform' => ['required', Rule::in(array_keys(SocialLink::platformLabels()))],
            'label' => ['required', 'string', 'max:80'],
            'url' => [
                'required',
                'string',
                'max:2048',
                'regex:/^(https?:\/\/|mailto:|tel:|#)/i',
            ],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'url.regex' => 'Link phải bắt đầu bằng http://, https://, mailto:, tel: hoặc #.',
        ]);

        $data['sort_order'] = (int) ($data['sort_order'] ?? 0);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
