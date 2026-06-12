@extends('adminlayout.app')

@section('admin_title', 'Cài đặt')

@section('content')
<div class="space-y-7">
    <div class="rounded-2xl border border-gray-200 bg-gradient-to-r from-white to-gray-50 px-6 py-5 shadow-sm">
        <h1 class="text-2xl md:text-3xl font-black tracking-tight text-gray-900">Cài đặt</h1>
        <p class="text-sm md:text-base text-gray-600 mt-1">Đổi logo và chỉnh chữ cạnh logo ngay tại đây.</p>
    </div>

    @if(session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-red-700 shadow-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <div class="xl:col-span-1 h-full rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-4">Logo hiện tại</p>
            <div class="rounded-2xl border-2 border-dashed border-gray-200 bg-gradient-to-b from-gray-50 to-white p-4 md:p-6">
                <div
                    class="mx-auto flex min-h-[11rem] w-full items-center justify-center rounded-2xl bg-white p-4 shadow-inner ring-1 ring-gray-100 md:min-h-[16rem] md:p-6 lg:min-h-[18rem]"
                >
                    <button
                        type="button"
                        class="group flex w-full max-w-[20rem] items-center justify-center rounded-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500/50 md:max-w-[22rem] lg:max-w-[24rem]"
                        title="Xem ảnh logo kích thước đầy đủ"
                        onclick="openLogoPreview()"
                    >
                        <img
                            src="{{ $currentLogoUrl }}"
                            alt="Logo hiện tại"
                            class="max-h-44 w-full object-contain rounded-xl shadow-md ring-1 ring-gray-200/90 transition duration-200 group-hover:scale-[1.02] group-hover:shadow-lg md:max-h-56 lg:max-h-64"
                        >
                    </button>
                </div>
                <p class="mt-3 text-center text-xs text-gray-500 md:text-sm">
                    Nhấn ảnh để xem lớn trong cửa sổ
                </p>
            </div>
        </div>

        <div class="xl:col-span-2 h-full rounded-2xl border border-gray-200 bg-white p-6 shadow-sm flex flex-col">
            <p class="text-xs uppercase tracking-wider text-gray-500 font-bold mb-4">{{ $setting ? 'Cập nhật logo' : 'Tạo logo' }}</p>

            <form
                action="{{ $setting ? route('admin.logo-settings.update') : route('admin.logo-settings.store') }}"
                method="POST"
                enctype="multipart/form-data"
                class="flex-1"
            >
                @csrf
                @if($setting)
                    @method('PUT')
                @endif

                <div class="h-full rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50 p-5 flex flex-col">
                    <div class="space-y-4">
                        <label for="logo_title" class="block space-y-2">
                            <span class="block text-sm font-semibold text-gray-800">Tiêu đề cạnh logo</span>
                            <input
                                id="logo_title"
                                type="text"
                                name="logo_title"
                                maxlength="60"
                                value="{{ old('logo_title', $setting->logo_title ?? '日本語') }}"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200"
                                placeholder="Ví dụ: 日本語"
                            >
                        </label>

                        <label for="logo_subtitle" class="block space-y-2">
                            <span class="block text-sm font-semibold text-gray-800">Mô tả cạnh logo</span>
                            <input
                                id="logo_subtitle"
                                type="text"
                                name="logo_subtitle"
                                maxlength="120"
                                value="{{ old('logo_subtitle', $setting->logo_subtitle ?? 'Học tiếng Nhật hiệu quả') }}"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200"
                                placeholder="Ví dụ: Học tiếng Nhật hiệu quả"
                            >
                        </label>

                        <label for="logo" class="block space-y-2">
                            <span class="block text-sm font-semibold text-gray-800">Chọn ảnh logo (tuỳ chọn)</span>
                            <input
                                id="logo"
                                type="file"
                                name="logo"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                                class="block w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 file:mr-4 file:rounded-lg file:border-0 file:bg-gray-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-gray-700"
                            >
                        </label>
                        <p class="text-xs text-gray-500">Hỗ trợ JPG, PNG, WEBP. Kích thước tối đa 2MB. Bạn có thể chỉ sửa chữ mà không cần đổi ảnh.</p>
                    </div>

                    <div class="mt-auto flex flex-wrap items-center gap-3 pt-5">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-xl bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition"
                        >
                            {{ $setting ? 'Lưu thay đổi' : 'Tạo logo' }}
                        </button>

                        @if($setting)
                            <button
                                type="submit"
                                form="delete-logo-setting-form"
                                class="inline-flex items-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition"
                            >
                                Xóa logo tùy chỉnh
                            </button>
                        @endif
                    </div>
                </div>
            </form>

            @if($setting)
                <form
                    id="delete-logo-setting-form"
                    action="{{ route('admin.logo-settings.destroy') }}"
                    method="POST"
                    onsubmit="return confirm('Xóa logo tùy chỉnh và quay về mặc định?')"
                >
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm md:p-6">
        <div class="mb-5 flex flex-col justify-between gap-3 md:flex-row md:items-start">
            <div>
                <p class="text-xs uppercase tracking-wider text-gray-500 font-bold">Mạng xã hội</p>
                <h2 class="mt-1 text-xl font-black tracking-tight text-gray-900">CRUD link social ở footer</h2>
                <p class="mt-1 text-sm text-gray-600">Quản lý icon, URL, thứ tự hiển thị và trạng thái bật/tắt cho các nút như Facebook, Twitter, Instagram, YouTube.</p>
            </div>
            <div class="flex gap-2 rounded-xl bg-gray-50 px-3 py-2 text-xs font-semibold text-gray-600">
                <span>{{ $socialLinks->where('is_active', true)->count() }} đang bật</span>
                <span class="text-gray-300">/</span>
                <span>{{ $socialLinks->count() }} tổng</span>
            </div>
        </div>

        <form action="{{ route('admin.social-links.store') }}" method="POST" class="mb-6 rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-4">
            @csrf
            <div class="grid grid-cols-1 gap-3 md:grid-cols-12">
                <label class="md:col-span-2">
                    <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500">Icon</span>
                    <select name="platform" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                        @foreach($socialPlatforms as $value => $label)
                            <option value="{{ $value }}" @selected(old('platform') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>

                <label class="md:col-span-2">
                    <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500">Tên</span>
                    <input name="label" value="{{ old('label') }}" placeholder="Facebook" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                </label>

                <label class="md:col-span-5">
                    <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500">URL</span>
                    <input name="url" value="{{ old('url', '#') }}" placeholder="https://facebook.com/your-page" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                </label>

                <label class="md:col-span-1">
                    <span class="mb-1 block text-xs font-bold uppercase tracking-wide text-gray-500">Thứ tự</span>
                    <input type="number" min="0" max="999" name="sort_order" value="{{ old('sort_order', 50) }}" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                </label>

                <label class="flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-3 py-2.5 md:col-span-1 md:self-end">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                    <span class="text-sm font-semibold text-gray-700">Bật</span>
                </label>

                <button type="submit" class="rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 md:col-span-1 md:self-end">
                    Thêm
                </button>
            </div>
        </form>

        @foreach($socialLinks as $socialLink)
            <form id="social-link-update-{{ $socialLink->id }}" action="{{ route('admin.social-links.update', $socialLink) }}" method="POST">
                @csrf
                @method('PUT')
            </form>
            <form id="social-link-delete-{{ $socialLink->id }}" action="{{ route('admin.social-links.destroy', $socialLink) }}" method="POST" onsubmit="return confirm('Xóa liên kết {{ $socialLink->label }}?')">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="overflow-x-auto rounded-2xl border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Preview</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Icon</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Tên</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500">URL</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Thứ tự</th>
                        <th class="px-4 py-3 text-left text-xs font-bold uppercase tracking-wide text-gray-500">Bật</th>
                        <th class="px-4 py-3 text-right text-xs font-bold uppercase tracking-wide text-gray-500">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($socialLinks as $socialLink)
                        <tr>
                            <td class="px-4 py-3">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-gray-800 text-gray-300">
                                    @include('components.social-icon', ['platform' => $socialLink->platform, 'class' => 'h-5 w-5'])
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <select name="platform" form="social-link-update-{{ $socialLink->id }}" class="min-w-36 rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                                    @foreach($socialPlatforms as $value => $label)
                                        <option value="{{ $value }}" @selected($socialLink->platform === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input name="label" form="social-link-update-{{ $socialLink->id }}" value="{{ $socialLink->label }}" class="min-w-32 rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                            </td>
                            <td class="px-4 py-3">
                                <input name="url" form="social-link-update-{{ $socialLink->id }}" value="{{ $socialLink->url }}" class="min-w-80 rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" min="0" max="999" name="sort_order" form="social-link-update-{{ $socialLink->id }}" value="{{ $socialLink->sort_order }}" class="w-24 rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-700 focus:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-200">
                            </td>
                            <td class="px-4 py-3">
                                <label class="inline-flex items-center gap-2">
                                    <input type="checkbox" name="is_active" value="1" form="social-link-update-{{ $socialLink->id }}" @checked($socialLink->is_active) class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span class="text-sm text-gray-600">{{ $socialLink->is_active ? 'Hiện' : 'Ẩn' }}</span>
                                </label>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <button type="submit" form="social-link-update-{{ $socialLink->id }}" class="rounded-lg bg-gray-900 px-3 py-2 text-xs font-bold text-white hover:bg-gray-700">
                                        Lưu
                                    </button>
                                    <button type="submit" form="social-link-delete-{{ $socialLink->id }}" class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-100">
                                        Xóa
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-sm text-gray-500">Chưa có link mạng xã hội nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<div
    id="logo-preview-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 p-4"
    onclick="closeLogoPreview(event)"
>
    <div class="relative w-full max-w-2xl rounded-2xl bg-white p-4 md:p-5 shadow-2xl" onclick="event.stopPropagation()">
        <button
            type="button"
            class="absolute right-3 top-3 inline-flex h-8 w-8 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-600 hover:bg-gray-100"
            onclick="closeLogoPreview()"
            aria-label="Đóng xem ảnh"
        >
            X
        </button>
        <div class="flex items-center justify-center rounded-xl bg-gray-50 p-3">
            <img
                src="{{ $currentLogoUrl }}"
                alt="Logo xem trước"
                class="max-h-[70vh] w-auto rounded-lg object-contain"
            >
        </div>
    </div>
</div>

<script>
    function openLogoPreview() {
        const modal = document.getElementById('logo-preview-modal');
        if (!modal) return;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeLogoPreview(event) {
        if (event && event.target && event.target.id !== 'logo-preview-modal') {
            return;
        }
        const modal = document.getElementById('logo-preview-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeLogoPreview();
        }
    });
</script>
@endsection
