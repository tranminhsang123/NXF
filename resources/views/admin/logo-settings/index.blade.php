@extends('adminlayout.app')

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
