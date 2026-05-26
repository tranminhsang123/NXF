@php
    $isSuperRole = $role->exists && $role->slug === 'super_admin';
@endphp

@if($errors->any())
    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">{{ $errors->first() }}</div>
@endif

<div class="bg-white rounded-lg shadow-sm p-6">
    <form method="POST" action="{{ $role->exists ? route('admin.admin-roles.update', $role) : route('admin.admin-roles.store') }}" class="space-y-6">
        @csrf
        @if($role->exists)
            @method('PUT')
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Tên vai trò</label>
                <input name="name"
                       value="{{ old('name', $role->name) }}"
                       class="w-full rounded-lg border border-gray-300 px-3 py-2 {{ $isSuperRole ? 'bg-gray-100 text-gray-500' : '' }}"
                       required
                       {{ $isSuperRole ? 'readonly' : '' }}>
            </div>
            <div>
                <label class="mb-2 block text-sm font-semibold text-gray-700">Slug</label>
                <input name="slug" value="{{ old('slug', $role->slug) }}" class="w-full rounded-lg border border-gray-300 px-3 py-2 font-mono {{ $isSuperRole ? 'bg-gray-100 text-gray-500' : '' }}" required {{ $isSuperRole ? 'readonly' : '' }}>
                <p class="mt-1 text-xs text-gray-500">Chỉ dùng chữ thường, số và dấu gạch dưới.</p>
            </div>
        </div>

        @if($isSuperRole)
            <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                Super Admin luôn giữ toàn bộ quyền hiện có để tránh tự khóa hệ thống.
            </div>
            @foreach($permissionGroups as $permissions)
                @foreach($permissions as $permission)
                    <input type="hidden" name="permission_ids[]" value="{{ $permission->id }}">
                @endforeach
            @endforeach
        @endif

        <div>
            <div class="mb-3 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Danh sách quyền</h2>
                <span class="text-xs text-gray-500">{{ collect($permissionGroups)->flatten(1)->count() }} quyền</span>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                @foreach($permissionGroups as $group => $permissions)
                    <div class="rounded-lg border border-gray-200 p-4">
                        <h3 class="mb-3 font-semibold text-gray-900">{{ str_replace('_', ' ', ucfirst($group)) }}</h3>
                        <div class="space-y-2">
                            @foreach($permissions as $permission)
                                <label class="flex items-start gap-2 rounded-lg px-2 py-2 hover:bg-gray-50">
                                    <input type="checkbox"
                                           name="permission_ids[]"
                                           value="{{ $permission->id }}"
                                           class="mt-1 rounded border-gray-300"
                                           @checked(in_array($permission->id, old('permission_ids', $selectedPermissionIds), true) || $isSuperRole)
                                           {{ $isSuperRole ? 'disabled' : '' }}>
                                    <span>
                                        <span class="block text-sm font-semibold text-gray-800">{{ $permission->name }}</span>
                                        <span class="block font-mono text-xs text-gray-500">{{ $permission->slug }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex flex-wrap gap-3">
            <button class="rounded-lg bg-red-600 px-5 py-2 font-semibold text-white hover:bg-red-700">Lưu vai trò</button>
            <a href="{{ route('admin.admin-roles.index') }}" class="rounded-lg bg-gray-200 px-5 py-2 font-semibold text-gray-700 hover:bg-gray-300">Huỷ</a>
        </div>
    </form>
</div>
